<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToServiceResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_results', function (Blueprint $table) {
            // Add missing columns without foreign key constraints first
            if (!Schema::hasColumn('service_results', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'patient_id')) {
                $table->unsignedBigInteger('patient_id')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'visit_id')) {
                $table->unsignedBigInteger('visit_id')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'result_type')) {
                $table->enum('result_type', ['text', 'numeric', 'file'])->nullable();
            }
            if (!Schema::hasColumn('service_results', 'result_text')) {
                $table->text('result_text')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'result_numeric')) {
                $table->decimal('result_numeric', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('service_results', 'result_file_path')) {
                $table->string('result_file_path')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'result_file_name')) {
                $table->string('result_file_name')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'status')) {
                $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected'])->default('draft');
            }
            if (!Schema::hasColumn('service_results', 'recorded_by')) {
                $table->unsignedBigInteger('recorded_by')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'approval_notes')) {
                $table->text('approval_notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_results', function (Blueprint $table) {
            // Drop columns if they exist
            $columnsToDrop = [
                'service_id', 'patient_id', 'visit_id', 'result_type', 
                'result_text', 'result_numeric', 'result_file_path', 
                'result_file_name', 'notes', 'status', 'recorded_by', 
                'approved_by', 'approved_at', 'approval_notes'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('service_results', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
