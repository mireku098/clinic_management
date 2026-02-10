<?php

namespace App\Http\Controllers;

use App\Models\PatientVisit;
use App\Models\Patient;
use App\Models\PatientService;
use App\Models\Package;
use App\Models\Service;
use App\Services\BillingService;
use App\Http\Requests\StoreVisitRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class VisitController extends Controller
{
    public function index(): View
    {
        \Log::info('VisitController index method called');
        
        $query = PatientVisit::with(['patient', 'attendingUser', 'services.service', 'package']);
        
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

    public function store(StoreVisitRequest $request)
    {
        // Check if this is an AJAX request
        $isAjax = $request->ajax() || $request->wantsJson();
        
        try {
            $data = $request->validated();
            
            // Auto-calculate BMI if weight and height provided
            if (isset($data['weight']) && isset($data['height']) && $data['weight'] && $data['height']) {
                $heightInMeters = $data['height'] / 100;
                $data['bmi'] = round($data['weight'] / ($heightInMeters * $heightInMeters), 1);
            }
            
            // Create the visit
            $visit = PatientVisit::create($data);
            
            // Handle package and service data separately (stored in patient_visits columns)
            $selectedPackage = $request->input('selected_package');
            $selectedServices = $request->input('selected_services');
            $totalAmount = $request->input('total_amount', 0);
            
            // Update visit with package and service data
            $visit->update([
                'total_amount' => $totalAmount,
                'selected_services' => $selectedServices,
                'selected_package' => $selectedPackage,
                'balance_due' => $totalAmount, // Initially, balance equals total amount
                'payment_status' => 'pending'
            ]);
            
            // Handle package_id if package selected
            if ($selectedPackage) {
                $packageData = json_decode($selectedPackage, true);
                if ($packageData && isset($packageData['id'])) {
                    $visit->update(['package_id' => $packageData['id']]);
                }
            }
            
            // Auto-create bill for this visit
            try {
                BillingService::createOrUpdateBillForVisit($visit);
                \Log::info("Bill created for visit {$visit->id}");
            } catch (\Exception $e) {
                \Log::error("Failed to create bill for visit {$visit->id}: " . $e->getMessage());
                // Continue with visit creation even if bill creation fails
            }
            
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Visit created successfully!',
                    'visit_id' => $visit->id
                ]);
            }
            
            return redirect()->route('visits')
                ->with('success', 'Visit created successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Error creating visit: ' . $e->getMessage());
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating visit: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating visit: ' . $e->getMessage());
        }
    }

    public function edit($id): View
    {
        try {
            \Log::info('Edit method called with ID: ' . $id);
            
            // Load visit with only essential relationships for form display
            $visit = PatientVisit::with(['patient'])->findOrFail($id);
            \Log::info('Visit found: ' . $visit->id);
            
            // Load packages and services for form dropdowns
            $packages = Package::where('status', 'active')->get();
            $services = Service::all();
            
            // Pre-populate selected services and package data from patient_visits columns
            $selectedServices = $visit->selected_services ? json_decode($visit->selected_services, true) : [];
            $selectedPackage = $visit->selected_package ? json_decode($visit->selected_package, true) : null;
            
            \Log::info('About to return edit view');
            return view('visits.edit', compact('visit', 'packages', 'services', 'selectedServices', 'selectedPackage'));
            
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
            
            // Update only the patient_visits record
            $visit->update($data);
            
            // Handle package and service data separately (stored in patient_visits columns)
            $selectedPackage = $request->input('selected_package');
            $selectedServices = $request->input('selected_services');
            $totalAmount = $request->input('total_amount', 0);
            
            // Update visit with package and service data
            $visit->update([
                'total_amount' => $totalAmount,
                'selected_services' => $selectedServices,
                'selected_package' => $selectedPackage,
                'balance_due' => $totalAmount, // Initially, balance equals total amount
                'payment_status' => 'pending'
            ]);
            
            // Handle package_id if package selected
            if ($selectedPackage) {
                $packageData = json_decode($selectedPackage, true);
                if ($packageData && isset($packageData['id'])) {
                    $visit->update(['package_id' => $packageData['id']]);
                }
            } else {
                $visit->update(['package_id' => null]);
            }
            
            // Auto-create bill for this visit (but don't override existing data)
            try {
                BillingService::createOrUpdateBillForVisit($visit);
                \Log::info("Bill created/updated for visit {$visit->id}");
            } catch (\Exception $e) {
                \Log::error("Failed to create bill for visit {$visit->id}: " . $e->getMessage());
                // Continue with visit update even if bill creation fails
            }
            
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Visit updated successfully!',
                    'visit_id' => $visit->id
                ]);
            }
            
            return redirect()->route('visits')
                ->with('success', 'Visit updated successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Error updating visit: ' . $e->getMessage());
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating visit: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating visit: ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $visit = PatientVisit::findOrFail($id);
            
            // Delete related patient services
            PatientService::where('visit_id', $id)->delete();
            
            // Delete related patient packages
            \App\Models\PatientPackage::where('visit_id', $id)->delete();
            
            // Delete the visit
            $visit->delete();
            
            return redirect()
                ->route('visits')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Visit Deleted',
                    'text' => 'Visit record has been deleted successfully.',
                    'showConfirmButton' => true,
                ]);
                
        } catch (\Exception $e) {
            \Log::error("Error deleting visit {$id}: " . $e->getMessage());
            
            return redirect()
                ->route('visits')
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Delete Failed',
                    'text' => 'Failed to delete visit: ' . $e->getMessage(),
                    'showConfirmButton' => true,
                ]);
        }
    }
}
