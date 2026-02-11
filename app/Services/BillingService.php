<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\PatientVisit;
use App\Models\PatientService;
use App\Models\PatientPackage;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BillingService
{
    /**
     * Create or update bill for a visit based on services and packages
     */
    public static function createOrUpdateBillForVisit(PatientVisit $visit): ?Bill
    {
        try {
            // Check if visit has any billable items
            $hasPackage = $visit->package_id && $visit->package;
            
            // Check for services both in relationship table and JSON data
            $hasServicesRelation = $visit->services()->exists();
            $hasServicesJson = $visit->selected_services && !empty(json_decode($visit->selected_services));
            $hasServices = $hasServicesRelation || $hasServicesJson;
            
            if (!$hasPackage && !$hasServices) {
                Log::info("Visit {$visit->id} has no billable items", [
                    'has_package' => $hasPackage,
                    'has_services_relation' => $hasServicesRelation,
                    'has_services_json' => $hasServicesJson,
                    'selected_services' => $visit->selected_services
                ]);
                return null;
            }
            
            Log::info("Visit {$visit->id} has billable items", [
                'has_package' => $hasPackage,
                'has_services' => $hasServices,
                'total_amount' => $visit->total_amount
            ]);
            
            // Check if bill already exists for this visit
            $existingBill = Bill::where('visit_id', $visit->id)->first();
            
            if ($existingBill) {
                // Update existing bill
                return self::updateExistingBill($existingBill, $visit);
            } else {
                // Create new bill
                return self::createNewBill($visit);
            }
            
        } catch (\Exception $e) {
            Log::error("Error creating bill for visit {$visit->id}: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Create new bill for visit
     */
    private static function createNewBill(PatientVisit $visit): Bill
    {
        $billType = $visit->package_id ? 'package' : 'service';
        $totalAmount = 0;
        
        // Calculate total amount
        if ($visit->package_id && $visit->package) {
            $totalAmount = $visit->package->total_cost;
        } else {
            // Calculate total from JSON services and relationship table
            $totalAmount = 0;
            
            // Add services from JSON data
            if ($visit->selected_services) {
                $servicesFromJson = json_decode($visit->selected_services, true) ?: [];
                foreach ($servicesFromJson as $serviceData) {
                    $totalAmount += $serviceData['price'] ?? 0;
                }
            }
            
            // Add services from relationship table (if any exist)
            if ($visit->services()->exists()) {
                $totalAmount += $visit->services()->sum('service_price');
            }
        }
        
        // Create bill
        // Generate unique bill number
        $billNumber = self::generateBillNumber();
        
        $bill = Bill::create([
            'bill_number' => $billNumber,
            'patient_id' => $visit->patient_id,
            'visit_id' => $visit->id,
            'bill_type' => $billType,
            'total_amount' => $totalAmount,
            'amount_paid' => 0,
            'balance' => $totalAmount,
            'status' => 'pending',
            'created_by' => auth()->check() ? auth()->id() : null,
            'notes' => "Auto-generated bill for visit #{$visit->id}",
        ]);
        
        // Create bill items
        self::createBillItems($bill, $visit);
        
        Log::info("Created new bill {$bill->id} for visit {$visit->id}");
        
        return $bill;
    }
    
    /**
     * Update existing bill
     */
    private static function updateExistingBill(Bill $bill, PatientVisit $visit): Bill
    {
        $totalAmount = 0;
        
        // Recalculate total amount
        if ($visit->package_id && $visit->package) {
            $totalAmount = $visit->package->total_cost;
        } else {
            // Calculate total from JSON services and relationship table
            $totalAmount = 0;
            
            // Add services from JSON data
            if ($visit->selected_services) {
                $servicesFromJson = json_decode($visit->selected_services, true) ?: [];
                foreach ($servicesFromJson as $serviceData) {
                    $totalAmount += $serviceData['price'] ?? 0;
                }
            }
            
            // Add services from relationship table (if any exist)
            if ($visit->services()->exists()) {
                $totalAmount += $visit->services()->sum('service_price');
            }
        }
        
        // Update bill
        $bill->update([
            'bill_type' => $visit->package_id ? 'package' : 'service',
            'total_amount' => $totalAmount,
            'balance' => $totalAmount - $bill->amount_paid,
            'notes' => $bill->notes . " | Updated on " . now()->format('Y-m-d H:i:s'),
        ]);
        
        // Remove old bill items and create new ones
        $bill->items()->delete();
        self::createBillItems($bill, $visit);
        
        Log::info("Updated bill {$bill->id} for visit {$visit->id}");
        
        return $bill;
    }
    
    /**
     * Create bill items for a visit
     */
    private static function createBillItems(Bill $bill, PatientVisit $visit): void
    {
        if ($visit->package_id && $visit->package) {
            // Create package bill item
            BillItem::create([
                'bill_id' => $bill->id,
                'package_id' => $visit->package_id,
                'service_id' => null,
                'description' => $visit->package->package_name,
                'quantity' => 1,
                'unit_price' => $visit->package->total_cost,
                'total_price' => $visit->package->total_cost,
                'item_type' => 'package',
                'notes' => 'Package billing',
            ]);
            
            // Add package services as individual items
            foreach ($visit->package->services as $packageService) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'service_id' => $packageService->service_id,
                    'package_id' => $visit->package_id,
                    'description' => $packageService->service_name ?? $packageService->service->service_name,
                    'quantity' => $packageService->sessions ?? 1,
                    'unit_price' => $packageService->unit_price ?? $packageService->service->price,
                    'total_price' => $packageService->service_total ?? $packageService->service->price,
                    'item_type' => 'service',
                    'notes' => 'Package service: ' . ($packageService->service_name ?? $packageService->service->service_name),
                ]);
            }
        }
        
        // Handle individual services - check both JSON and relationship table
        $servicesFromJson = [];
        if ($visit->selected_services) {
            $servicesFromJson = json_decode($visit->selected_services, true) ?: [];
        }
        
        // Add services from JSON data
        if (!empty($servicesFromJson)) {
            foreach ($servicesFromJson as $serviceData) {
                $service = Service::find($serviceData['id']);
                if ($service) {
                    BillItem::create([
                        'bill_id' => $bill->id,
                        'service_id' => $service->id,
                        'package_id' => null,
                        'description' => $service->service_name,
                        'quantity' => 1,
                        'unit_price' => $serviceData['price'] ?? $service->price,
                        'total_price' => $serviceData['price'] ?? $service->price,
                        'item_type' => 'service',
                        'notes' => 'Individual service from selection',
                    ]);
                }
            }
        }
        
        // Add services from relationship table (if any exist)
        if ($visit->services()->exists()) {
            $services = $visit->services;
            
            foreach ($services as $patientService) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'service_id' => $patientService->service_id,
                    'package_id' => null,
                    'description' => $patientService->service->service_name,
                    'quantity' => 1,
                    'unit_price' => $patientService->service_price,
                    'total_price' => $patientService->service_price,
                    'item_type' => 'service',
                    'notes' => 'Individual service from relationship',
                ]);
            }
        }
    }
    
    /**
     * Create bills for all visits that don't have bills but have services/packages
     */
    public static function createMissingBills(): int
    {
        try {
            $visitsWithoutBills = PatientVisit::with(['patient', 'package', 'services.service'])
                ->whereDoesntHave('bill')
                ->where(function($query) {
                    $query->whereNotNull('package_id')
                          ->orWhereHas('services');
                })
                ->get();
            
            $createdCount = 0;
            
            foreach ($visitsWithoutBills as $visit) {
                try {
                    self::createOrUpdateBillForVisit($visit);
                    $createdCount++;
                } catch (\Exception $e) {
                    Log::error("Failed to create bill for visit {$visit->id}: " . $e->getMessage());
                }
            }
            
            Log::info("Created {$createdCount} missing bills");
            return $createdCount;
            
        } catch (\Exception $e) {
            Log::error("Error creating missing bills: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Generate a unique bill number
     */
    private static function generateBillNumber(): string
    {
        do {
            $billNumber = 'BILL-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (Bill::where('bill_number', $billNumber)->exists());
        
        return $billNumber;
    }
}
