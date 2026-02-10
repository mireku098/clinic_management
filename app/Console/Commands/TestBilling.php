<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Models\Package;
use App\Models\PatientVisit;
use App\Services\BillingService;
use Illuminate\Console\Command;

class TestBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test automatic bill creation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing automatic bill creation...');

        try {
            // Get test data
            $patient = Patient::first();
            $package = Package::first();
            
            if (!$patient || !$package) {
                $this->error('No test patient or package found');
                return 1;
            }
            
            // Create test visit
            $visit = PatientVisit::create([
                'patient_id' => $patient->id,
                'visit_date' => now(),
                'package_id' => $package->id,
                'total_amount' => $package->total_cost,
                'balance_due' => $package->total_cost,
                'payment_status' => 'pending'
            ]);
            
            $this->info("Created visit #{$visit->id} for patient {$patient->first_name} {$patient->last_name}");
            
            // Test automatic bill creation
            $bill = BillingService::createOrUpdateBillForVisit($visit);
            
            if ($bill) {
                $this->info("✅ Successfully created bill #{$bill->id} for visit #{$visit->id}");
                $this->info("Bill type: {$bill->bill_type}");
                $this->info("Total amount: {$bill->total_amount}");
                $this->info("Bill items count: " . $bill->items()->count());
                
                return 0;
            } else {
                $this->error("❌ Failed to create bill for visit #{$visit->id}");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}
