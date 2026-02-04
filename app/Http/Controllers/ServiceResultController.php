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
            $patient = Patient::findOrFail($request->patient_id);
            $visit = \App\Models\PatientVisit::findOrFail($request->visit_id);
            $service = Service::findOrFail($request->service_id);
            
            // Verify the visit belongs to the patient
            if ($visit->patient_id !== $patient->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visit does not belong to this patient'
                ], 403);
            }
            
            // Check for existing result
            $existingResult = ServiceResult::where('patient_id', $patient->id)
                ->where('visit_id', $visit->id)
                ->where('service_id', $service->id)
                ->first();
            
            // Prevent editing approved results
            if ($existingResult && $existingResult->status === 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Approved results cannot be edited'
                ], 403);
            }
            
            // Validate based on service result type
            $rules = [
                'status' => 'required|in:draft,pending_approval,approved,rejected',
                'notes' => 'nullable|string|max:1000',
                'recorded_at' => 'required|date',
            ];
            
            if ($service->result_type === 'numeric') {
                $rules['result_numeric'] = 'required|numeric';
            } elseif ($service->result_type === 'text') {
                $rules['result_text'] = 'required|string|max:5000';
            } elseif ($service->result_type === 'file') {
                if (!$existingResult) {
                    $rules['result_file'] = 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png';
                }
            }
            
            $request->validate($rules);
            
            // Find or create the patient_service record
            $patientService = \App\Models\PatientService::where('patient_id', $patient->id)
                ->where('visit_id', $visit->id)
                ->where('service_id', $service->id)
                ->first();
            
            if (!$patientService) {
                // Create patient_service record if it doesn't exist
                $patientService = \App\Models\PatientService::create([
                    'patient_id' => $patient->id,
                    'visit_id' => $visit->id,
                    'service_id' => $service->id,
                    'service_price' => $service->price ?? 0,
                    'status' => 'completed',
                ]);
            }
            
            $data = [
                'patient_id' => $patient->id,
                'visit_id' => $visit->id,
                'service_id' => $service->id,
                'patient_service_id' => $patientService->id,
                'result_type' => $service->result_type,
                'status' => $request->status,
                'notes' => $request->notes,
                'recorded_by' => auth()->id() ?? 1,
                'recorded_at' => \Carbon\Carbon::parse($request->recorded_at),
            ];
            
            // Handle result value based on service type - set other result fields to NULL
            if ($service->result_type === 'text') {
                $data['result_text'] = $request->result_text;
                $data['result_numeric'] = null;
                $data['result_file_path'] = null;
                $data['result_file_name'] = null;
            } elseif ($service->result_type === 'numeric') {
                $data['result_numeric'] = $request->result_numeric;
                $data['result_text'] = null;
                $data['result_file_path'] = null;
                $data['result_file_name'] = null;
            } elseif ($service->result_type === 'file') {
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
     */
    public function patientServiceResults($patientId): View
    {
        $patient = Patient::findOrFail($patientId);
        
        // Get all service results for this patient with relationships
        $serviceResults = ServiceResult::with(['service', 'visit', 'recorder', 'approver'])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get all visits for this patient to show available services
        $visits = PatientVisit::with(['services.service'])
            ->where('patient_id', $patientId)
            ->orderBy('visit_date', 'desc')
            ->get();
        
        // Get services that don't have results yet
        $availableServices = [];
        foreach ($visits as $visit) {
            foreach ($visit->services as $patientService) {
                if ($patientService->service) {
                    $hasResult = $serviceResults->contains('service_id', $patientService->service_id);
                    if (!$hasResult) {
                        $availableServices[] = [
                            'service' => $patientService->service,
                            'visit' => $visit,
                            'patient_service_id' => $patientService->id
                        ];
                    }
                }
            }
        }
        
        return view('service_results.patient_timeline', compact(
            'patient', 
            'serviceResults', 
            'visits', 
            'availableServices'
        ));
    }

    /**
     * Show dedicated Service Result page
     */
    public function showResultPage($patientId, $visitId, $serviceId): View
    {
        try {
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
            
        } catch (\Exception $e) {
            \Log::error('Error loading service result page: ' . $e->getMessage());
            abort(404, 'Service Result page not found');
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
        $query = ServiceResult::with(['service', 'patient', 'visit', 'recorder', 'approver']);
        
        // Filter by patient
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        
        // Filter by service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
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
                'pagination' => $results->links()->toHtml(),
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
        $patients = Patient::all();
        $visits = [];
        
        if ($request->filled('patient_id')) {
            $visits = PatientVisit::where('patient_id', $request->patient_id)->latest()->get();
        }
        
        return view('service-results.create', compact('services', 'patients', 'visits'));
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
        $result = ServiceResult::findOrFail($id);
        
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
            
            $validated = $request->validate([
                'service_id' => 'required|exists:services,id',
                'patient_id' => 'required|exists:patients,id',
                'visit_id' => 'nullable|exists:patient_visits,id',
                'result_type' => 'required|in:text,numeric,file',
                'result_text' => 'required_if:result_type,text|nullable|string',
                'result_numeric' => 'required_if:result_type,numeric|nullable|numeric',
                'result_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'notes' => 'nullable|string',
            ]);
            
            $data = [
                'service_id' => $validated['service_id'],
                'patient_id' => $validated['patient_id'],
                'visit_id' => $validated['visit_id'] ?? null,
                'result_type' => $validated['result_type'],
                'notes' => $validated['notes'] ?? null,
            ];
            
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
            
            return redirect()->route('service-results.index')
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
}
