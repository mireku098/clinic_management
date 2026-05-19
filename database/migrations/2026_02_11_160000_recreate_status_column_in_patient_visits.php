<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateStatusColumnInPatientVisits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            // Drop existing status column
            $table->dropColumn('status');
        });
        
        Schema::table('patient_visits', function (Blueprint $table) {
            // Recreate status column with only pending and completed
            $table->enum('status', ['pending', 'completed'])->default('pending')->after('visit_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            // Drop the new status column
            $table->dropColumn('status');
        });
        
        Schema::table('patient_visits', function (Blueprint $table) {
            // Recreate the old status column (this would need to know the original values)
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled')->after('visit_type');
        });
    }
}
