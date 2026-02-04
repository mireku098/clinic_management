<?php

namespace App\Http\Controllers;

use App\Models\PatientVisit;
use App\Models\Patient;
use App\Models\Package;
use App\Models\Service;
use App\Models\PatientService;
use App\Http\Requests\StoreVisitRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class VisitController extends Controller
{
    public function index(): View
    {
        \Log::info('VisitController index method called');
        
        $query = PatientVisit::with(['patient', 'attendingUser', 'services.service']);
        
        // Search by patient name or code
        if (request()->filled('patient_search')) {
            $search = request('patient_search');
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('patient_code', 'like', '%' . $search . '%');
            });
        }
        
        // Filter by visit date
        if (request()->filled('visit_date')) {
            $query->whereDate('visit_date', request('visit_date'));
        }
        
        // Filter by visit type
        if (request()->filled('visit_type') && request('visit_type') !== '') {
            $query->where('visit_type', request('visit_type'));
        }
        
        // Filter by status (for now, all visits are 'completed')
        if (request()->filled('status_filter') && request('status_filter') !== '') {
            // Status filtering logic can be added here when status field is implemented
            // For now, all visits show as completed
        }
        
        $visits = $query->orderBy('visit_date', 'desc')
                        ->orderBy('visit_time', 'desc')
                        ->paginate(20)
                        ->appends(request()->query()); // Preserve query parameters in pagination
        
        \Log::info('Visits loaded: ' . $visits->total());
        
        return view('visits', compact('visits'));
    }

    public function show($id): View
    {
        $visit = PatientVisit::with(['patient', 'attendingUser', 'services.service'])
            ->findOrFail($id);
            
        // Ensure dates are Carbon instances
        if (is_string($visit->visit_date)) {
            $visit->visit_date = \Carbon\Carbon::parse($visit->visit_date);
        }
        if (is_string($visit->visit_time)) {
            $visit->visit_time = \Carbon\Carbon::parse($visit->visit_time);
        }
        if (is_string($visit->created_at)) {
            $visit->created_at = \Carbon\Carbon::parse($visit->created_at);
        }
        if (is_string($visit->updated_at)) {
            $visit->updated_at = \Carbon\Carbon::parse($visit->updated_at);
        }
            
        return view('visits.show', compact('visit'));
    }

    public function create(): View
    {
        \Log::info('VisitController create method called');
        
        $patient = null;
        
        // Check if patient parameter is provided
        if (request()->has('patient')) {
            $patientCode = request('patient');
            $patient = Patient::where('patient_code', $patientCode)->first();
        }
        
        try {
            // Load active packages for selection
            $packages = Package::where('status', 'active')->get();
            \Log::info('Packages loaded successfully: ' . $packages->count());
        } catch (\Exception $e) {
            \Log::error('Error loading packages: ' . $e->getMessage());
            $packages = collect(); // Empty collection as fallback
        }
        
        try {
            // Load all available services
            $services = Service::all();
            \Log::info('Services loaded successfully: ' . $services->count());
        } catch (\Exception $e) {
            \Log::error('Error loading services: ' . $e->getMessage());
            $services = collect(); // Empty collection as fallback
        }
        
        \Log::info('About to return visits.add view');
        
        return view('visits.add', compact('patient', 'packages', 'services'));
    }

    public function edit($id): View
    {
        try {
            \Log::info('Edit method called with ID: ' . $id);
            
            $visit = PatientVisit::with(['patient', 'attendingUser', 'services', 'package'])->findOrFail($id);
            $patient = $visit->patient;
            \Log::info('Visit found: ' . $visit->id);
            
            // Load packages and services for the form
            $packages = Package::where('status', 'active')->get();
            $services = Service::all();
            
            \Log::info('About to return edit view');
            return view('visits.edit', compact('visit', 'patient', 'packages', 'services'));
            
        } catch (\Exception $e) {
            \Log::error('Error in edit method: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(StoreVisitRequest $request, $id)
    {
        $visit = PatientVisit::findOrFail($id);
        
        // Check if this is an AJAX request
        $isAjax = $request->ajax() || $request->wantsJson();
        
        try {
            $data = $request->validated();
            
            // Auto-calculate BMI if weight and height provided
            if (isset($data['weight']) && isset($data['height']) && $data['weight'] && $data['height']) {
                $heightInMeters = $data['height'] / 100;
                $data['bmi'] = round($data['weight'] / ($heightInMeters * $heightInMeters), 1);
            }
            
            $visit->update($data);
            
            // Handle package and service data
            $selectedPackage = $request->input('selected_package');
            $selectedServices = $request->input('selected_services');
            $totalAmount = $request->input('total_amount', 0);
            
            // Update visit with package and service data
            $visit->update([
                'total_amount' => $totalAmount,
                'selected_services' => $selectedServices,
                'selected_package' => $selectedPackage,
                'balance_due' => $totalAmount, // Initially, balance equals total amount
            ]);
            
            // If package is selected, update package_id and create patient package record
            if ($selectedPackage) {
                $packageData = json_decode($selectedPackage, true);
                if ($packageData && isset($packageData['id'])) {
                    $visit->update(['package_id' => $packageData['id']]);
                    
                    // Delete existing patient package records for this visit
                    \App\Models\PatientPackage::where('visit_id', $visit->id)->delete();
                    
                    // Create new patient package record
                    \App\Models\PatientPackage::create([
                        'patient_id' => $visit->patient_id,
                        'package_id' => $packageData['id'],
                        'visit_id' => $visit->id,
                        'start_date' => now(),
                        'status' => 'active',
                        'created_at' => now()
                    ]);
                    
                    // Delete existing patient services for this visit (package services)
                    PatientService::where('visit_id', $visit->id)->delete();
                    
                    // Add package services to patient services
                    $package = Package::find($packageData['id']);
                    if ($package) {
                        foreach ($package->services as $packageService) {
                            PatientService::create([
                                'patient_id' => $visit->patient_id,
                                'visit_id' => $visit->id,
                                'service_id' => $packageService->service_id,
                                'service_price' => $packageService->service->price ?? 0,
                                'status' => 'pending',
                            ]);
                        }
                    }
                }
            }
            
            // If individual services are selected, create patient service records
            if ($selectedServices) {
                $servicesData = json_decode($selectedServices, true);
                if ($servicesData && is_array($servicesData)) {
                    // Delete existing individual services (keep package services if any)
                    PatientService::where('visit_id', $visit->id)->delete();
                    
                    foreach ($servicesData as $serviceData) {
                        if (isset($serviceData['id'])) {
                            PatientService::create([
                                'patient_id' => $visit->patient_id,
                                'visit_id' => $visit->id,
                                'service_id' => $serviceData['id'],
                                'service_price' => $serviceData['price'] ?? 0,
                                'status' => 'pending',
                            ]);
                        }
                    }
                }
            }
            
            // Return JSON response for AJAX requests
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Visit updated successfully',
                    'visit_id' => $visit->id
                ]);
            }
            
            return redirect()
                ->route('visits')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Visit Updated',
                    'text' => 'Patient visit has been updated successfully.',
                ]);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Let validation exceptions pass through to show proper errors
            throw $e;
            
        } catch (\Throwable $exception) {
            \Log::error('Visit update failed: ' . $exception->getMessage());
            
            // Return JSON response for AJAX requests
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the visit: ' . $exception->getMessage()
                ], 500);
            }
            
            return back()
                ->withInput()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Unable to Update Visit',
                    'text' => 'An error occurred while updating the visit: ' . $exception->getMessage(),
                    'showConfirmButton' => true,
                ]);
        }
    }

    public function destroy($id)
    {
        $visit = PatientVisit::findOrFail($id);
        
        // Check if this is an AJAX request
        $isAjax = request()->ajax() || request()->wantsJson();
        
        try {
            // Use soft delete - this will set deleted_at timestamp instead of actually deleting
            $visit->delete();
            
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Visit deleted successfully!'
                ], 200);
            }
            
            return redirect()
                ->route('visits')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Visit Deleted',
                    'text' => 'Patient visit has been deleted successfully.',
                ]);
                
        } catch (\Throwable $exception) {
            \Log::error('Visit deletion failed: ' . $exception->getMessage());
            \Log::error('Visit deletion stack trace: ' . $exception->getTraceAsString());
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the visit: ' . $exception->getMessage(),
                    'errors' => []
                ], 500);
            }
            
            return back()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Unable to Delete Visit',
                    'text' => 'An error occurred while deleting the visit: ' . $exception->getMessage(),
                    'showConfirmButton' => true,
                ]);
        }
    }

    public function store(StoreVisitRequest $request)
    {
        // Check if this is an AJAX request
        $isAjax = $request->ajax() || $request->wantsJson();
        
        try {
            $data = $request->validated();
            
            // Add created_at timestamp
            $data['created_at'] = now();
            
            // Set attended_by to current authenticated user
            if (auth()->check()) {
                $data['attended_by'] = auth()->id();
            } else {
                $data['attended_by'] = null;
            }
            
            // Auto-calculate BMI if weight and height provided
            if (isset($data['weight']) && isset($data['height']) && $data['weight'] && $data['height']) {
                $heightInMeters = $data['height'] / 100;
                $data['bmi'] = round($data['weight'] / ($heightInMeters * $heightInMeters), 1);
            }
            
            $visit = PatientVisit::create($data);
            
            // Handle package and service data
            $selectedPackage = $request->input('selected_package');
            $selectedServices = $request->input('selected_services');
            $totalAmount = $request->input('total_amount', 0);
            
            // Update visit with package and service data
            $visit->update([
                'total_amount' => $totalAmount,
                'selected_services' => $selectedServices,
                'selected_package' => $selectedPackage,
                'balance_due' => $totalAmount, // Initially, balance equals total amount
            ]);
            
            // If package is selected, update package_id and create patient package record
            if ($selectedPackage) {
                $packageData = json_decode($selectedPackage, true);
                if ($packageData && isset($packageData['id'])) {
                    $visit->update(['package_id' => $packageData['id']]);
                    
                    // Create patient package record
                    \App\Models\PatientPackage::create([
                        'patient_id' => $visit->patient_id,
                        'package_id' => $packageData['id'],
                        'visit_id' => $visit->id,
                        'start_date' => now(),
                        'status' => 'active',
                        'created_at' => now()
                    ]);
                    
                    // Add package services to patient services
                    $package = Package::find($packageData['id']);
                    if ($package) {
                        foreach ($package->services as $packageService) {
                            PatientService::create([
                                'patient_id' => $visit->patient_id,
                                'visit_id' => $visit->id,
                                'service_id' => $packageService->service_id,
                                'service_price' => $packageService->service->price ?? 0,
                                'status' => 'pending',
                            ]);
                        }
                    }
                }
            }
            
            // If individual services are selected, create patient service records
            if ($selectedServices) {
                $servicesData = json_decode($selectedServices, true);
                if ($servicesData && is_array($servicesData)) {
                    foreach ($servicesData as $serviceData) {
                        if (isset($serviceData['id'])) {
                            PatientService::create([
                                'patient_id' => $visit->patient_id,
                                'visit_id' => $visit->id,
                                'service_id' => $serviceData['id'],
                                'service_price' => $serviceData['price'] ?? 0,
                                'status' => 'pending',
                            ]);
                        }
                    }
                }
            }
            
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Visit recorded successfully!',
                    'data' => $visit->toArray()
                ], 200);
            }
            
            return redirect()
                ->route('visits')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Visit Recorded',
                    'text' => 'Patient visit has been recorded successfully.',
                ]);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
            
        } catch (\Throwable $exception) {
            \Log::error('Visit creation failed: ' . $exception->getMessage());
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to record visit. Please try again.',
                    'error' => $exception->getMessage()
                ], 500);
            }
            
            return back()
                ->withInput()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Unable to Record Visit',
                    'text' => 'An error occurred while recording the visit. Please try again.',
                    'showConfirmButton' => true,
                ]);
        }
    }
}
