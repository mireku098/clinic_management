<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackageFieldsToServiceResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_results', function (Blueprint $table) {
            // Add package_id column (nullable, for direct package results)
            if (!Schema::hasColumn('service_results', 'package_id')) {
                $table->unsignedBigInteger('package_id')->nullable();
            }
            
            // Add patient_package_id column (nullable, for patient package results)
            if (!Schema::hasColumn('service_results', 'patient_package_id')) {
                $table->unsignedBigInteger('patient_package_id')->nullable();
            }
            
            // Add foreign key constraints
            if (!Schema::hasColumn('service_results', 'package_id')) {
                $table->foreign('package_id')->references('id')->on('packages')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('service_results', 'patient_package_id')) {
                $table->foreign('patient_package_id')->references('id')->on('patient_packages')->onDelete('set null');
            }
            
            // Add indexes for better performance
            if (!Schema::hasColumn('service_results', 'package_id')) {
                $table->index('package_id');
            }
            
            if (!Schema::hasColumn('service_results', 'patient_package_id')) {
                $table->index('patient_package_id');
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
            // Drop foreign key constraints first
            $table->dropForeign(['package_id']);
            $table->dropForeign(['patient_package_id']);
            
            // Drop columns
            $table->dropColumn(['package_id', 'patient_package_id']);
            
            // Drop indexes
            $table->dropIndex(['package_id']);
            $table->dropIndex(['patient_package_id']);
        });
    }
}
