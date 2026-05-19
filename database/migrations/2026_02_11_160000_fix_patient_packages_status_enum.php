<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixPatientPackagesStatusEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_packages', function (Blueprint $table) {
            // Drop existing status column
            $table->dropColumn('status');
        });
        
        Schema::table('patient_packages', function (Blueprint $table) {
            // Recreate status column with correct ENUM values
            $table->enum('status', ['pending', 'completed'])->default('pending')->after('package_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_packages', function (Blueprint $table) {
            // Revert to original ENUM values
            $table->dropColumn('status');
        });
        
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->enum('status', ['active', 'completed', 'expired', 'cancelled'])->default('active')->after('package_price');
        });
    }
}
