<?php

namespace App\Http\Controllers;

use App\Models\ServiceResult;
use App\Models\Service;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\PatientService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceResultController extends Controller
{
    /**
     * Save service result (create or update)
     */
    public function saveResult(Request $request)
    {
        try {
            // VALIDATION: Exactly one of service_id or package_id must be present
            $hasServiceId = $request->has('service_id') && $request->service_id != '';
            $hasPackageId = $request->has('package_id') && $request->package_id != '';
            
            if ($hasServiceId && $hasPackageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot specify both service_id and package_id. Choose one.',
                    'errors' => ['service_or_package' => ['Specify either service or package, not both']]
                ], 422);
            }
            
            if (!$hasServiceId && !$hasPackageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Must specify either service_id or package_id.',
                    'errors' => ['service_or_package' => ['Service or package is required']]
                ], 422);
            }
            
            // Load patient and visit
            $patient = Patient::findOrFail($request->patient_id);
            $visit = \App\Models\PatientVisit::findOrFail($request->visit_id);
            
            // Verify visit belongs to patient
            if ($visit->patient_id !== $patient->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visit does not belong to this patient'
                ], 403);
            }
            
            // Handle service or package
            if ($hasServiceId) {
                $service = Service::findOrFail($request->service_id);
                $serviceId = $service->id;
                $packageId = null;
                
                // Use result_type from form, fallback to service default
                $resultType = $request->result_type ?? $service->result_type;
                
                // Check for existing result
                $existingResult = ServiceResult::where('patient_id', $patient->id)
                    ->where('visit_id', $visit->id)
                    ->where('service_id', $serviceId)
                    ->first();
                
                // Prevent editing approved results
                if ($existingResult && $existingResult->status === 'approved') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Approved results cannot be edited'
                    ], 403);
                }
                
                // Create patient_service record if needed
                $patientService = PatientService::where('patient_id', $patient->id)
                    ->where('visit_id', $visit->id)
                    ->where('service_id', $serviceId)
                    ->first();
                
                if (!$patientService) {
                    $patientService = \App\Models\PatientService::create([
                        'patient_id' => $patient->id,
                        'visit_id' => $visit->id,
                        'service_id' => $serviceId,
                        'service_price' => $service->price ?? 0,
                        'status' => 'completed',
                    ]);
                }
                
                $patientServiceId = $patientService->id;
                
            } else { // hasPackageId
                $package = \App\Models\Package::findOrFail($request->package_id);
                $serviceId = null;
                $packageId = $package->id;
                
                // Use result_type from form, fallback to text for packages
                $resultType = $request->result_type ?? 'text';
                
                // Check for existing result
                $existingResult = ServiceResult::where('patient_id', $patient->id)
                    ->where('visit_id', $visit->id)
                    ->where('package_id', $packageId)
                    ->first();
                
                // Prevent editing approved results
                if ($existingResult && $existingResult->status === 'approved') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Approved results cannot be edited'
                    ], 403);
                }
                
                // Create or find patient package record
                $patientPackage = \App\Models\PatientPackage::where('patient_id', $patient->id)
                    ->where('visit_id', $visit->id)
                    ->where('package_id', $packageId)
                    ->first();
                
                if (!$patientPackage) {
                    $patientPackage = \App\Models\PatientPackage::create([
                        'patient_id' => $patient->id,
                        'visit_id' => $visit->id,
                        'package_id' => $packageId,
                        'package_price' => $package->price ?? 0,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                $patientServiceId = null;
            }
            
            // Validate based on result type
            $rules = [
                'result_type' => 'required|in:text,numeric,file',
                'status' => 'required|in:draft,pending_approval,approved,rejected',
                'notes' => 'nullable|string|max:1000',
                'recorded_at' => 'required|date',
            ];
            
            if ($resultType === 'numeric') {
                $rules['result_numeric'] = 'required|numeric';
            } elseif ($resultType === 'text') {
                $rules['result_text'] = 'required|string|max:5000';
            } elseif ($resultType === 'file') {
                if (!$existingResult) {
                    $rules['result_file'] = 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png';
                }
            }
            
            $request->validate($rules);
            
            // Prepare data array
            $data = [
                'patient_id' => $patient->id,
                'visit_id' => $visit->id,
                'service_id' => $serviceId,
                'package_id' => $packageId,
                'patient_service_id' => $patientServiceId,
                'patient_package_id' => isset($patientPackage) ? $patientPackage->id : null,
                'result_type' => $resultType,
                'status' => $request->status,
                'notes' => $request->notes,
                'recorded_by' => auth()->id() ?? 1,
                'recorded_at' => \Carbon\Carbon::parse($request->recorded_at),
            ];
            
            // Handle result value based on result type
            if ($resultType === 'text') {
                $data['result_text'] = $request->result_text;
                $data['result_numeric'] = null;
                $data['result_file_path'] = null;
                $data['result_file_name'] = null;
            } elseif ($resultType === 'numeric') {
                $data['result_numeric'] = $request->result_numeric;
                $data['result_text'] = null;
                $data['result_file_path'] = null;
                $data['result_file_name'] = null;
            } elseif ($resultType === 'file') {
                if ($request->hasFile('result_file')) {
                    $file = $request->file('result_file');
                    if ($file->isValid()) {
                        $filename = 'service-result-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('service-results', $filename, 'public');
                        $data['result_file_path'] = $path;
                        $data['result_file_name'] = $filename;
                    }
                }
                $data['result_text'] = null;
                $data['result_numeric'] = null;
            }
            
            // Handle approval
            if ($request->status === 'approved') {
                $data['approved_by'] = auth()->id() ?? 1;
                $data['approved_at'] = now();
            }
            
            if ($existingResult) {
                $existingResult->update($data);
                $serviceResult = $existingResult;
                $message = 'Service result updated successfully';
            } else {
                $serviceResult = ServiceResult::create($data);
                $message = 'Service result created successfully';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $serviceResult->load(['service', 'visit', 'recorder'])
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving service result: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving service result: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show dedicated Service Result page
        $patient = Patient::findOrFail($patientId);
        $visit = \App\Models\PatientVisit::findOrFail($visitId);
        $service = Service::findOrFail($serviceId);
            
        // Verify the visit belongs to the patient
        if ($visit->patient_id !== $patient->id) {
            abort(404, 'Visit does not belong to this patient');
        }
            
        // Ensure visit_date is a Carbon instance
        if (is_string($visit->visit_date)) {
            $visit->visit_date = \Carbon\Carbon::parse($visit->visit_date);
        }
            
        // Check for existing result
        $existingResult = ServiceResult::where('patient_id', $patient->id)
            ->where('visit_id', $visit->id)
            ->where('service_id', $service->id)
            ->first();
            
        // Ensure result timestamps are Carbon instances
        if ($existingResult) {
            if (is_string($existingResult->recorded_at)) {
                $existingResult->recorded_at = \Carbon\Carbon::parse($existingResult->recorded_at);
            }
            if (is_string($existingResult->created_at)) {
                $existingResult->created_at = \Carbon\Carbon::parse($existingResult->created_at);
            }
            if (is_string($existingResult->updated_at)) {
                $existingResult->updated_at = \Carbon\Carbon::parse($existingResult->updated_at);
            }
        }
            
        // Get package information if applicable
        $package = null;
        if ($visit->package_id) {
            $package = \App\Models\Package::find($visit->package_id);
        }
            
        return view('service_results.form', compact(
            'patient', 
            'visit', 
            'service', 
            'existingResult', 
            'package'
        ));
     */
    public function showResultPage($patientId, $visitId, $serviceId)
    {
        try {
            $patient = Patient::findOrFail($patientId);
            $visit = \App\Models\PatientVisit::findOrFail($visitId);
            $service = Service::findOrFail($serviceId);
            
            // Verify visit belongs to patient
            if ($visit->patient_id !== $patient->id) {
                abort(404, 'Visit does not belong to this patient');
            }
            
            // Ensure visit_date is a Carbon instance
            if (is_string($visit->visit_date)) {
                $visit->visit_date = \Carbon\Carbon::parse($visit->visit_date);
            }
            
            // Check for existing result
            $existingResult = ServiceResult::where('patient_id', $patient->id)
                ->where('visit_id', $visit->id)
                ->where('service_id', $service->id)
                ->first();
            
            // If result exists, redirect to edit
            if ($existingResult) {
                return redirect()->route('service-results.edit', $existingResult->id);
            }
            
            // If no result exists, redirect to create with pre-filled parameters
            return redirect()->route('service-results.create', [
                'patient_id' => $patient->id,
                'service_id' => $service->id,
                'visit_id' => $visit->id
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading service result page: ' . $e->getMessage());
            abort(404, 'Service Result page not found');
        }
    }

    /**
     * Show patient service results timeline
     */
    public function patientServiceResults($patientId): View
    {
        $patient = Patient::findOrFail($patientId);
        
        // Get all service results for this patient with relationships (including packages)
        $serviceResults = ServiceResult::with(['service', 'package', 'patientPackage', 'visit', 'recorder', 'approver'])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get all visits for this patient to show available services and packages
        $visits = PatientVisit::with(['services.service', 'package'])
            ->where('patient_id', $patientId)
            ->orderBy('visit_date', 'desc')
            ->get();
        
        // Get services and packages that don't have results yet
        $availableServices = [];
        $mostRecentVisit = $visits->first(); // Get most recent visit
        
        if ($mostRecentVisit) {
            // Get existing results for this specific visit
            $visitResults = $serviceResults->where('visit_id', $mostRecentVisit->id);
            
            // Check for services from JSON column (selected_services)
            if ($mostRecentVisit->selected_services) {
                $selectedServices = json_decode($mostRecentVisit->selected_services, true);
                if ($selectedServices) {
                    foreach ($selectedServices as $serviceData) {
                        $serviceId = $serviceData['id'];
                        $hasResult = $visitResults->contains('service_id', $serviceId);
                        if (!$hasResult) {
                            $service = \App\Models\Service::find($serviceId);
                            if ($service) {
                                $availableServices[] = [
                                    'service' => $service,
                                    'visit' => $mostRecentVisit,
                                    'service_data' => $serviceData
                                ];
                            }
                        }
                    }
                }
            }
            
            // Check for package from JSON column (selected_package)
            if ($mostRecentVisit->selected_package) {
                $selectedPackage = json_decode($mostRecentVisit->selected_package, true);
                if ($selectedPackage) {
                    $packageId = $selectedPackage['id'];
                    $hasPackageResult = $visitResults->contains('package_id', $packageId);
                    if (!$hasPackageResult) {
                        $package = \App\Models\Package::find($packageId);
                        if ($package) {
                            $availableServices[] = [
                                'package' => $package,
                                'visit' => $mostRecentVisit,
                                'package_data' => $selectedPackage
                            ];
                        }
                    }
                }
            }
        }
        
        return view('service-results.patient_timeline', compact(
            'patient', 
            'serviceResults', 
            'visits', 
            'availableServices'
        ));
    }

    /**
     * Show service result details for patient
     */
    public function showForPatient($patientId, $resultId)
    {
        try {
            $result = ServiceResult::with(['service', 'package', 'patient', 'visit', 'recorder'])
                ->where('id', $resultId)
                ->where('patient_id', $patientId)
                ->firstOrFail();
            
            return view('service-results.show', compact('result'));
        } catch (\Exception $e) {
            \Log::error('Error loading service result details: ' . $e->getMessage());
            abort(404, 'Service Result not found');
        }
    }

    /**
     * Show edit form for patient service result
     */
    public function editForPatient($patientId, $resultId)
    {
        try {
            $result = ServiceResult::with(['service', 'package', 'patient', 'visit'])
                ->where('id', $resultId)
                ->where('patient_id', $patientId)
                ->firstOrFail();
            
            if (!$result->isEditable()) {
                return back()->with('error', 'This result cannot be edited in its current status.');
            }
            
            $services = Service::where('status', 'active')->get();
            $patients = Patient::all();
            $visits = PatientVisit::where('patient_id', $patientId)->latest()->get();
            
            return view('service-results.edit', compact('result', 'services', 'patients', 'visits'));
        } catch (\Exception $e) {
            \Log::error('Error loading edit form: ' . $e->getMessage());
            abort(404, 'Service Result not found');
        }
    }

    /**
     * Show service result details for API
     */
    public function showApi($resultId)
    {
        try {
            $serviceResult = ServiceResult::with(['service', 'visit', 'recorder'])
                ->findOrFail($resultId);
            
            return response()->json([
                'success' => true,
                'data' => $serviceResult
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading service result: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update service result from patient context
     */
    public function updateFromPatientContext(Request $request)
    {
        try {
            $serviceResult = ServiceResult::findOrFail($request->result_id);
            
            // Only allow editing draft results
            if ($serviceResult->status !== 'draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft results can be edited'
                ], 403);
            }
            
            $data = [
                'status' => $request->status,
                'notes' => $request->notes,
            ];
            
            // Update result value based on type (only if not file)
            if ($serviceResult->result_type !== 'file') {
                if ($serviceResult->result_type === 'text') {
                    $data['result_text'] = $request->result_value;
                } elseif ($serviceResult->result_type === 'numeric') {
                    $data['result_numeric'] = $request->result_value;
                }
            }
            
            $serviceResult->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Service result updated successfully',
                'data' => $serviceResult->load(['service', 'visit', 'recorder'])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating service result: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store service result from patient context
     */
    public function storeFromPatientContext(Request $request)
    {
        try {
            $patient = Patient::where('patient_code', $request->patient_code)->firstOrFail();
            
            // Find or create the patient_service record
            $patientService = PatientService::where('patient_id', $patient->id)
                ->where('service_id', $request->service_id)
                ->first();
            
            if (!$patientService) {
                // Create patient_service record if it doesn't exist
                $patientService = PatientService::create([
                    'patient_id' => $patient->id,
                    'service_id' => $request->service_id,
                    'service_price' => Service::find($request->service_id)->price ?? 0,
                    'status' => 'completed',
                ]);
            }
            
            $data = [
                'patient_id' => $patient->id,
                'service_id' => $request->service_id,
                'patient_service_id' => $patientService->id,
                'result_type' => $request->result_type,
                'status' => in_array($request->status, ['draft', 'pending_approval', 'approved', 'rejected']) ? $request->status : 'draft',
                'notes' => $request->notes,
                'recorded_by' => auth()->id() ?? 1,
                'recorded_at' => now(),
            ];
            
            // Handle different result types
            if ($request->result_type === 'text') {
                $data['result_text'] = $request->result_value;
            } elseif ($request->result_type === 'numeric') {
                $data['result_numeric'] = $request->result_value;
            } elseif ($request->result_type === 'file' && $request->hasFile('result_file')) {
                $file = $request->file('result_file');
                if ($file->isValid()) {
                    $filename = 'service-result-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('service-results', $filename, 'public');
                    $data['result_file_path'] = $path;
                    $data['result_file_name'] = $filename;
                }
            }
            
            $serviceResult = ServiceResult::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Service result saved successfully',
                'data' => $serviceResult->load(['service', 'visit'])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving service result: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = ServiceResult::with(['service', 'patient', 'visit', 'recorder', 'approver', 'package', 'patientPackage']);
        
        // Filter by patient
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        
        // Filter by service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        
        // Filter by package
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by result type
        if ($request->filled('result_type')) {
            $query->where('result_type', $request->result_type);
        }
        
        $results = $query->latest()->paginate(15);
        
        // Check if AJAX request
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'html' => view('service-results.partials.results-grid', compact('results'))->render(),
                'pagination' => method_exists($results, 'links') ? $results->links()->toHtml() : '',
                'count' => $results->total()
            ]);
        }
        
        return view('service-results.index', compact('results'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $services = Service::where('status', 'active')->get();
        
        // If we have a specific service_id, make sure it's included even if not active
        if ($request->filled('service_id')) {
            $specificService = Service::find($request->service_id);
            if ($specificService && !$services->contains('id', $specificService->id)) {
                $services->push($specificService);
            }
        }
        
        $patients = Patient::all();
        $visits = [];
        $patient = null;
        
        if ($request->filled('patient_id')) {
            $patient = Patient::find($request->patient_id);
            $visits = PatientVisit::where('patient_id', $request->patient_id)->latest()->get();
            
            // If we also have a specific visit_id, make sure it's included
            if ($request->filled('visit_id')) {
                $specificVisit = PatientVisit::find($request->visit_id);
                if ($specificVisit && !$visits->contains('id', $specificVisit->id)) {
                    $visits->push($specificVisit);
                }
            }
        }
        
        return view('service-results.create', compact('services', 'patients', 'visits', 'patient'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'service_id' => 'required|exists:services,id',
                'patient_id' => 'required|exists:patients,id',
                'visit_id' => 'nullable|exists:patient_visits,id',
                'result_type' => 'required|in:text,numeric,file',
                'result_text' => 'required_if:result_type,text|nullable|string',
                'result_numeric' => 'required_if:result_type,numeric|nullable|numeric',
                'result_file' => 'required_if:result_type,file|nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'notes' => 'nullable|string',
            ]);
            
            $data = [
                'service_id' => $validated['service_id'],
                'patient_id' => $validated['patient_id'],
                'visit_id' => $validated['visit_id'] ?? null,
                'result_type' => $validated['result_type'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
                'recorded_by' => auth()->id(),
            ];
            
            // Handle result based on type
            if ($validated['result_type'] === 'text') {
                $data['result_text'] = $validated['result_text'];
            } elseif ($validated['result_type'] === 'numeric') {
                $data['result_numeric'] = $validated['result_numeric'];
            } elseif ($validated['result_type'] === 'file' && $request->hasFile('result_file')) {
                $file = $request->file('result_file');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('service-results', $fileName, 'public');
                
                $data['result_file_path'] = $filePath;
                $data['result_file_name'] = $file->getClientOriginalName();
            }
            
            $result = ServiceResult::create($data);
            
            // Check if AJAX request
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Service result created successfully!',
                    'result' => $result
                ]);
            }
            
            return redirect()->route('service-results.index')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Success!',
                    'text' => 'Service result created successfully!'
                ]);
                
        } catch (\Exception $e) {
            // Handle AJAX errors
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating service result: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error creating service result: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = ServiceResult::with(['service', 'patient', 'visit', 'recorder', 'approver'])
                               ->findOrFail($id);
        
        return view('service-results.show', compact('result'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = ServiceResult::with(['service', 'package', 'patient', 'visit'])->findOrFail($id);
        
        if (!$result->isEditable()) {
            return back()->with('error', 'This result cannot be edited in its current status.');
        }
        
        $services = Service::where('status', 'active')->get();
        $patients = Patient::all();
        $visits = PatientVisit::where('patient_id', $result->patient_id)->latest()->get();
        
        return view('service-results.edit', compact('result', 'services', 'patients', 'visits'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $result = ServiceResult::findOrFail($id);
            
            if (!$result->isEditable()) {
                throw new \Exception('This result cannot be edited in its current status.');
            }
            
            $rules = [
                'patient_id' => 'required|exists:patients,id',
                'visit_id' => 'nullable|exists:patient_visits,id',
                'result_type' => 'required|in:text,numeric,file',
                'result_text' => 'required_if:result_type,text|nullable|string',
                'result_numeric' => 'required_if:result_type,numeric|nullable|numeric',
                'result_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'status' => 'required|in:draft,pending_approval,approved,rejected',
                'recorded_at' => 'required|date',
                'notes' => 'nullable|string',
            ];
            
            // For package results, require package_id instead of service_id
            if ($result->package_id) {
                $rules['package_id'] = 'required|exists:packages,id';
            } else {
                $rules['service_id'] = 'required|exists:services,id';
            }
            
            $validated = $request->validate($rules);
            
            $data = [
                'patient_id' => $validated['patient_id'],
                'visit_id' => $validated['visit_id'] ?? null,
                'result_type' => $validated['result_type'],
                'status' => $validated['status'],
                'recorded_at' => \Carbon\Carbon::parse($validated['recorded_at']),
                'notes' => $validated['notes'] ?? null,
            ];
            
            // Add service_id or package_id based on result type
            if ($result->package_id) {
                $data['package_id'] = $validated['package_id'];
                $data['service_id'] = null;
            } else {
                $data['service_id'] = $validated['service_id'];
                $data['package_id'] = null;
            }
            
            // Handle result based on type
            if ($validated['result_type'] === 'text') {
                $data['result_text'] = $validated['result_text'];
                $data['result_numeric'] = null;
            } elseif ($validated['result_type'] === 'numeric') {
                $data['result_numeric'] = $validated['result_numeric'];
                $data['result_text'] = null;
            } elseif ($validated['result_type'] === 'file') {
                if ($request->hasFile('result_file')) {
                    // Delete old file if exists
                    if ($result->result_file_path) {
                        Storage::disk('public')->delete($result->result_file_path);
                    }
                    
                    $file = $request->file('result_file');
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('service-results', $fileName, 'public');
                    
                    $data['result_file_path'] = $filePath;
                    $data['result_file_name'] = $file->getClientOriginalName();
                }
            }
            
            $result->update($data);
            
            // Check if AJAX request
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Service result updated successfully!',
                    'result' => $result
                ]);
            }
            
            return redirect()->route('patients.service-results', $result->patient_id)
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Success!',
                    'text' => 'Service result updated successfully!'
                ]);
                
        } catch (\Exception $e) {
            // Handle AJAX errors
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating service result: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error updating service result: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $result = ServiceResult::findOrFail($id);
            
            if (!$result->isEditable()) {
                throw new \Exception('This result cannot be deleted in its current status.');
            }
            
            // Delete file if exists
            if ($result->result_file_path) {
                Storage::disk('public')->delete($result->result_file_path);
            }
            
            $result->delete();
            
            // Check if AJAX request
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Service result deleted successfully!'
                ]);
            }
            
            return redirect()->route('service-results.index')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Success!',
                    'text' => 'Service result deleted successfully!'
                ]);
                
        } catch (\Exception $e) {
            // Handle AJAX errors
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting service result: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error deleting service result: ' . $e->getMessage());
        }
    }
    
    /**
     * Submit result for approval.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function submitForApproval(Request $request, $id)
    {
        try {
            $result = ServiceResult::findOrFail($id);
            
            if (!$result->isEditable()) {
                throw new \Exception('This result cannot be submitted in its current status.');
            }
            
            $result->update([
                'status' => 'pending_approval'
            ]);
            
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Result submitted for approval successfully!'
                ]);
            }
            
            return back()->with('success', 'Result submitted for approval successfully!');
            
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error submitting result: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error submitting result: ' . $e->getMessage());
        }
    }
    
    /**
     * Approve or reject result.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approveResult(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'approval_notes' => 'nullable|string'
            ]);
            
            $result = ServiceResult::findOrFail($id);
            
            if ($result->status !== 'pending_approval') {
                throw new \Exception('This result is not pending approval.');
            }
            
            $result->update([
                'status' => $validated['action'] === 'approve' ? 'approved' : 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $validated['approval_notes']
            ]);
            
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => "Result {$validated['action']}d successfully!"
                ]);
            }
            
            return back()->with('success', "Result {$validated['action']}d successfully!");
            
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing approval: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error processing approval: ' . $e->getMessage());
        }
    }
    
    /**
     * Show service results for a specific patient
     */
    public function patientResultsIndex($patientId): View
    {
        $patient = Patient::findOrFail($patientId);
        $serviceResults = ServiceResult::with(['service', 'patient', 'visit'])
            ->where('patient_id', $patientId)
            ->latest()
            ->get();
        
        // Debug: Log what we found
        \Log::info('Patient Results Debug', [
            'patient_id' => $patientId,
            'patient_name' => $patient->first_name . ' ' . $patient->last_name,
            'results_count' => $serviceResults->count(),
            'results_data' => $serviceResults->toArray()
        ]);
            
        return view('service-results.patient-index', compact('serviceResults', 'patient'));
    }
    
    /**
     * Show create form for patient service result
     */
    public function createForPatient($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $services = Service::where('status', 'active')->get();
        $visits = PatientVisit::where('patient_id', $patientId)
            ->latest()
            ->get();
        
        return view('service-results.create', compact('patient', 'services', 'visits'));
    }
    
    /**
     * Save service result for patient
     */
    public function saveForPatient(Request $request, $patientId)
    {
        // Use the same saveResult logic but with patient context
        return $this->saveResult($request);
    }
}
