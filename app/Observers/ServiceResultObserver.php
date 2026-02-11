<?php

namespace App\Observers;

use App\Models\ServiceResult;
use App\Models\PatientVisit;
use Illuminate\Support\Facades\Log;

class ServiceResultObserver
{
    /**
     * Handle the ServiceResult "updated" event.
     */
    public function updated(ServiceResult $serviceResult)
    {
        // Only proceed if status was changed to 'approved'
        if ($serviceResult->isDirty('status') && $serviceResult->status === 'approved') {
            
            // Get the associated visit
            $visit = $serviceResult->visit;
            
            if ($visit) {
                // Update visit completion status
                $visit->updateCompletionStatus();
                
                Log::info('Service result approved, checking visit completion status', [
                    'service_result_id' => $serviceResult->id,
                    'visit_id' => $visit->id,
                    'new_visit_status' => $visit->status
                ]);
            }
        }
    }

    /**
     * Handle the ServiceResult "created" event.
     */
    public function created(ServiceResult $serviceResult)
    {
        // When a new service result is created, ensure visit is pending
        $visit = $serviceResult->visit;
        
        if ($visit && $visit->status === 'completed') {
            $visit->update(['status' => 'pending']);
            
            Log::info('New service result created, visit status set to pending', [
                'service_result_id' => $serviceResult->id,
                'visit_id' => $visit->id
            ]);
        }
    }
}
