<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyBillsTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('bills', 'patient_id')) {
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('bills', 'visit_id')) {
                $table->foreignId('visit_id')->nullable()->constrained('patient_visits')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('bills', 'bill_type')) {
                $table->enum('bill_type', ['package', 'service', 'combined'])->default('service');
            }
            
            if (!Schema::hasColumn('bills', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0);
            }
            
            if (!Schema::hasColumn('bills', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0);
            }
            
            if (!Schema::hasColumn('bills', 'balance')) {
                $table->decimal('balance', 10, 2)->default(0);
            }
            
            if (!Schema::hasColumn('bills', 'status')) {
                $table->enum('status', ['pending', 'partial', 'paid', 'cancelled'])->default('pending');
            }
            
            if (!Schema::hasColumn('bills', 'notes')) {
                $table->text('notes')->nullable();
            }
            
            if (!Schema::hasColumn('bills', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            }
            
            // Add indexes
            $table->index(['patient_id', 'status']);
            $table->index(['visit_id', 'status']);
            $table->index('bill_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['visit_id']);
            $table->dropForeign(['created_by']);
            $table->dropIndex(['patient_id', 'status']);
            $table->dropIndex(['visit_id', 'status']);
            $table->dropIndex(['bill_type']);
            
            $table->dropColumn([
                'patient_id',
                'visit_id', 
                'bill_type',
                'total_amount',
                'amount_paid',
                'balance',
                'status',
                'notes',
                'created_by'
            ]);
        });
    }
}
