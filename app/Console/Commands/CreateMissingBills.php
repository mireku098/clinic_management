<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BillingService;

class CreateMissingBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:create-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create missing bills for visits that have services or packages but no bills';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to create missing bills...');
        
        try {
            $createdCount = BillingService::createMissingBills();
            
            $this->info("Successfully created {$createdCount} missing bills.");
            
            if ($createdCount > 0) {
                $this->info('Billing system is now up to date!');
            } else {
                $this->info('No missing bills found. All visits with services/packages already have bills.');
            }
            
        } catch (\Exception $e) {
            $this->error("Error creating missing bills: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
