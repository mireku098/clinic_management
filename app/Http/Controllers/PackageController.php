<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageService;
use App\Models\Service;
use App\Http\Requests\StorePackageRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

class PackageController extends Controller
{
    /**
     * Update an existing package.
     */
    public function update(StorePackageRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            
            // Validate the request data
            $validated = $this->validatePackageData($request);
            
            // Find the existing package
            $package = Package::findOrFail($id);
            
            // Update package
            $package->update([
                'package_name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'result_type' => $validated['resultType'],
                'duration_weeks' => $validated['totalWeeks'],
                'total_cost' => $this->calculateTotalCost($validated['services']),
                'status' => $validated['status'],
                'category' => $validated['category'] ?? null
            ]);
            
            // Delete existing package services
            PackageService::where('package_id', $package->id)->delete();
            
            // Create new package services
            $this->createPackageServices($package->id, $validated['services']);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Package updated successfully!',
                'data' => [
                    'package' => $package->fresh('services')
                ]
            ]);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error updating package: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => $e->getMessage()
            ], 500);
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating package: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the package',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request): JsonResponse
    {
        try {
            Log::info('Package creation attempt', ['request_data' => $request->all()]);
            
            // Validate the request data
            $validated = $this->validatePackageData($request);
            
            // Use database transaction to ensure data integrity
            DB::beginTransaction();
            
            try {
                // Create the package
                $package = $this->createPackage($validated);
                
                // Create package services
                $this->createPackageServices($package->id, $validated['services']);
                
                // Commit the transaction
                DB::commit();
                
                Log::info('Package created successfully', [
                    'package_id' => $package->id,
                    'package_name' => $package->package_name
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Package created successfully!',
                    'data' => [
                        'package' => $package->load('services'),
                        'redirect_url' => route('packages')
                    ]
                ], 201);
                
            } catch (\Exception $e) {
                // Roll back the transaction if anything fails
                DB::rollBack();
                
                Log::error('Package creation failed during transaction', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'validated_data' => $validated
                ]);
                
                throw $e;
            }
            
        } catch (ValidationException $e) {
            Log::warning('Package validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error during package creation', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            
            $errorMessage = 'Database error occurred';
            
            // Check for specific constraint violations
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                $errorMessage = 'Invalid service selected';
            } elseif (str_contains($e->getMessage(), 'unique constraint')) {
                $errorMessage = 'Package with this name already exists';
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error' => $e->getMessage()
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Unexpected error during package creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while creating the package',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Validate package data
     */
    private function validatePackageData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'resultType' => 'required|in:text,numeric,file',
            'durationValue' => 'required|integer|min:1',
            'durationType' => 'required|in:weeks,months',
            'totalWeeks' => 'required|integer|min:1',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'services' => 'required|array|min:1',
            'services.*.serviceId' => 'required|integer|exists:services,id',
            'services.*.serviceName' => 'required|string',
            'services.*.costPerSession' => 'required|numeric|min:0',
            'services.*.frequencyType' => 'required|in:once,per_week,per_month',
            'services.*.frequencyValue' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    // Get the service index from the attribute name
                    preg_match('/services\.(\d+)\./', $attribute, $matches);
                    $index = $matches[1] ?? null;
                    
                    if ($index !== null) {
                        $frequencyType = request("services.{$index}.frequencyType");
                        
                        // Frequency value should only be required for per_week and per_month
                        if ($frequencyType === 'once' && $value > 1) {
                            $fail('For "once" frequency, value should be 1');
                        }
                    }
                }
            ],
            'services.*.totalSessions' => 'required|integer|min:1',
            'services.*.totalCost' => 'required|numeric|min:0'
        ], [
            'name.required' => 'Package name is required',
            'durationValue.required' => 'Duration value is required',
            'durationValue.min' => 'Duration must be at least 1',
            'totalWeeks.required' => 'Total weeks calculation is required',
            'services.required' => 'At least one service must be added',
            'services.min' => 'At least one service must be added',
            'services.*.serviceId.exists' => 'Selected service is invalid',
            'services.*.costPerSession.min' => 'Cost per session must be at least 0',
            'services.*.frequencyType.in' => 'Invalid frequency type',
            'services.*.frequencyValue.min' => 'Frequency value must be at least 1'
        ]);
    }
    
    /**
     * Create package record
     */
    private function createPackage(array $validated): Package
    {
        // Calculate total cost from services only (no package fee)
        $calculatedTotalCost = $this->calculateTotalCost($validated['services']);
        
        return Package::create([
            'package_name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'result_type' => $validated['resultType'],
            'duration_weeks' => $validated['totalWeeks'],
            'total_cost' => $calculatedTotalCost,
            'status' => $validated['status'],
            'package_code' => $this->generatePackageCode(),
            'category' => $validated['category'] ?? null
        ]);
    }
    
    /**
     * Create package services
     */
    private function createPackageServices(int $packageId, array $services): void
    {
        foreach ($services as $serviceData) {
            PackageService::create([
                'package_id' => $packageId,
                'service_id' => $serviceData['serviceId'],
                'unit_price' => $serviceData['costPerSession'],
                'frequency_type' => $serviceData['frequencyType'],
                'frequency_value' => $serviceData['frequencyValue'],
                'sessions' => $serviceData['totalSessions'],
                'service_total' => $serviceData['totalCost']
            ]);
        }
    }
    
    /**
     * Calculate total cost from services only
     */
    private function calculateTotalCost(array $services): float
    {
        return collect($services)->sum('totalCost');
    }
    
    /**
     * Generate a unique package code.
     *
     * @return string
     */
    private function generatePackageCode(): string
    {
        do {
            $code = 'PKG-' . strtoupper(Str::random(6));
        } while (Package::where('package_code', $code)->exists());
        
        return $code;
    }
}
