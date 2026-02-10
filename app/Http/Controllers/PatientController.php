<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class PatientController extends Controller
{
    /**
     * Store a newly created patient in storage.
     */
    public function store(StorePatientRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            $data['age'] = Carbon::parse($data['date_of_birth'])->age;
            $data['patient_code'] = $this->generatePatientCode();
            $data['registered_at'] = now();

            // Handle photo upload
            if ($request->hasFile('patient_photo')) {
                $file = $request->file('patient_photo');
                
                if ($file->isValid()) {
                    // Store the file and get the path
                    $path = $file->store('patient-photos', 'public');
                    $data['photo_path'] = $path;
                } else {
                    return back()
                        ->withInput()
                        ->with('swal', [
                            'icon' => 'error',
                            'title' => 'Photo Upload Failed',
                            'text' => 'The uploaded file is not valid. Please try again.',
                            'showConfirmButton' => true,
                        ]);
                }
            }

            Patient::create($data);

            return redirect()
                ->route('patients')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Patient Registered',
                    'text' => 'Patient record has been created successfully.',
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Unable to Register',
                    'text' => 'An unexpected error occurred. Please try again.',
                    'showConfirmButton' => true,
                ]);
        }
    }

    public function update(StorePatientRequest $request, Patient $patient): RedirectResponse
    {
        // Debug: Log all request data
        \Log::info('=== UPDATE REQUEST DEBUG ===');
        \Log::info('All request data: ' . json_encode($request->all()));
        \Log::info('All files: ' . json_encode($request->allFiles()));
        \Log::info('Has patient_photo: ' . ($request->hasFile('patient_photo') ? 'YES' : 'NO'));
        
        try {
            $data = $request->validated();

            $data['age'] = Carbon::parse($data['date_of_birth'])->age;

            // Handle photo upload
            if ($request->hasFile('patient_photo')) {
                \Log::info('Photo file detected in update request');
                $file = $request->file('patient_photo');
                \Log::info('File details: ' . json_encode([
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'error' => $file->getError(),
                    'is_valid' => $file->isValid()
                ]));
                
                if ($file->isValid()) {
                    // Store the file and get the path
                    $path = $file->store('patient-photos', 'public');
                    $data['photo_path'] = $path;
                    \Log::info('Photo uploaded successfully: ' . $path);
                } else {
                    \Log::error('Photo upload failed: ' . $file->getErrorMessage());
                    return back()
                        ->withInput()
                        ->with('swal', [
                            'icon' => 'error',
                            'title' => 'Photo Upload Failed',
                            'text' => 'The uploaded file is not valid. Please try again.',
                            'showConfirmButton' => true,
                        ]);
                }
            } else {
                \Log::info('No photo file detected in update request');
            }

            $patient->update($data);
            \Log::info('Patient updated successfully');

            return redirect()
                ->route('patients')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Patient Updated',
                    'text' => 'Patient record has been updated successfully.',
                ]);
        } catch (Throwable $exception) {
            \Log::error('Patient update failed: ' . $exception->getMessage());
            report($exception);

            return back()
                ->withInput()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Unable to Update',
                    'text' => 'An unexpected error occurred. Please try again.',
                    'showConfirmButton' => true,
                ]);
        }
    }

    private function generatePatientCode(): string
    {
        do {
            $code = 'PAT-' . strtoupper(Str::random(6));
        } while (Patient::where('patient_code', $code)->exists());

        return $code;
    }

    /**
     * Get patient visits for API
     */
    public function getPatientVisits($patientId)
    {
        try {
            $patient = Patient::findOrFail($patientId);
            $visits = $patient->visits()->latest()->get(['id', 'created_at']);
            
            return response()->json([
                'success' => true,
                'visits' => $visits
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading visits: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show patient visits page
     */
    public function visits($patientCode)
    {
        try {
            $patient = Patient::where('patient_code', $patientCode)->firstOrFail();
            $visits = $patient->visits()->with(['services'])->latest()->get();
            
            return view('patients.visits', compact('patient', 'visits'));
        } catch (\Exception $e) {
            \Log::error('Error loading patient visits: ' . $e->getMessage());
            abort(404, 'Patient not found');
        }
    }

    /**
     * Display patient context page
     */
    public function context(Request $request)
    {
        $patientCode = $request->get('patient');
        
        if (!$patientCode) {
            return redirect()->route('patients')
                ->with('swal', [
                    'icon' => 'warning',
                    'title' => 'Patient Not Specified',
                    'text' => 'Please select a patient to view their context.',
                ]);
        }
        
        try {
            $contextData = \App\Services\PatientContextService::getPatientContextData($patientCode);
            
            return view('patients.context', [
                'patient' => $contextData['patient'],
                'patientId' => $patientCode,
                'contextData' => $contextData,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('patients')
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Patient Not Found',
                    'text' => "Patient with code {$patientCode} was not found.",
                ]);
        } catch (\Exception $e) {
            \Log::error("Error loading patient context: " . $e->getMessage());
            
            return redirect()->route('patients')
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Error Loading Patient',
                    'text' => 'An error occurred while loading patient data. Please try again.',
                ]);
        }
    }

    /**
     * Get patient overview data for API
     */
    public function getOverviewData($patientCode)
    {
        try {
            $contextData = \App\Services\PatientContextService::getPatientContextData($patientCode);
            
            return response()->json([
                'success' => true,
                'data' => $contextData['overview']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading overview data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient vitals data for API
     */
    public function getVitalsData($patientCode)
    {
        try {
            $contextData = \App\Services\PatientContextService::getPatientContextData($patientCode);
            
            return response()->json([
                'success' => true,
                'data' => $contextData['vitals']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading vitals data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient medical history data for API
     */
    public function getMedicalHistoryData($patientCode)
    {
        try {
            $contextData = \App\Services\PatientContextService::getPatientContextData($patientCode);
            
            return response()->json([
                'success' => true,
                'data' => $contextData['medicalHistory']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading medical history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient billing data for API
     */
    public function getBillingData($patientCode)
    {
        try {
            $contextData = \App\Services\PatientContextService::getPatientContextData($patientCode);
            
            return response()->json([
                'success' => true,
                'data' => $contextData['billing']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading billing data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient visits data for API
     */
    public function getVisitsData($patientCode)
    {
        try {
            $patient = Patient::where('patient_code', $patientCode)->firstOrFail();
            $visits = $patient->visits()->latest()->get(['id', 'visit_date', 'purpose', 'notes', 'created_at']);
            
            return response()->json([
                'success' => true,
                'data' => $visits
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading visits data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient bills data for API
     */
    public function getBillsData($patientCode)
    {
        try {
            $patient = Patient::where('patient_code', $patientCode)->firstOrFail();
            $bills = $patient->bills()->latest()->get();
            
            $totalBilled = $bills->sum('total_amount');
            $totalPaid = $bills->sum('amount_paid');
            $outstanding = $totalBilled - $totalPaid;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'bills' => $bills,
                    'summary' => [
                        'total_billed' => $totalBilled,
                        'total_paid' => $totalPaid,
                        'outstanding' => $outstanding,
                        'active_bills' => $bills->where('status', '!=', 'paid')->count()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading bills data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get patient service results data for API
     */
    public function getServiceResultsData($patientCode)
    {
        try {
            $patient = Patient::where('patient_code', $patientCode)->firstOrFail();
            
            // Get service results directly by patient_id
            $serviceResults = \App\Models\ServiceResult::where('patient_id', $patient->id)
            ->with(['service', 'visit'])
            ->latest()
            ->get();
            
            return response()->json([
                'success' => true,
                'data' => $serviceResults
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading service results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all patients for API (used in billing dropdown)
     */
    public function getPatientsApi()
    {
        try {
            $patients = Patient::select('id', 'first_name', 'last_name', 'patient_code')
                ->orderBy('first_name')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $patients
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading patients: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search patients for API
     */
    public function searchPatients(Request $request)
    {
        try {
            $searchTerm = $request->get('q', '');
            
            if (strlen($searchTerm) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search term must be at least 2 characters'
                ]);
            }
            
            $patients = Patient::where(function($query) use ($searchTerm) {
                $query->where('first_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('patient_code', 'like', '%' . $searchTerm . '%')
                      ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%');
            })
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'patient_code', 'phone', 'email']);
            
            return response()->json([
                'success' => true,
                'patients' => $patients
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get patient data as JSON for BMI calculation
     */
    public function getPatientJson($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'patient' => [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'patient_code' => $patient->patient_code,
                    'height' => $patient->height,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found: ' . $e->getMessage()
            ], 404);
        }
    }
}
