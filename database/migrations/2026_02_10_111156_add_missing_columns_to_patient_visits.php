<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToPatientVisits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            // Add missing columns from original migration
            if (!Schema::hasColumn('patient_visits', 'history_present_illness')) {
                $table->text('history_present_illness')->nullable();
            }
            if (!Schema::hasColumn('patient_visits', 'assessment')) {
                $table->text('assessment')->nullable();
            }
            if (!Schema::hasColumn('patient_visits', 'treatment_plan')) {
                $table->text('treatment_plan')->nullable();
            }
            if (!Schema::hasColumn('patient_visits', 'practitioner')) {
                $table->string('practitioner')->nullable();
            }
            if (!Schema::hasColumn('patient_visits', 'department')) {
                $table->string('department')->nullable();
            }
            if (!Schema::hasColumn('patient_visits', 'oxygen_saturation')) {
                $table->integer('oxygen_saturation')->nullable();
            }
            if (!Schema::hasColumn('patient_visits', 'pulse_rate')) {
                $table->integer('pulse_rate')->nullable();
            }
            if (!Schema::hasColumn('patient_visits', 'reason_for_visit')) {
                $table->text('reason_for_visit')->nullable();
            }
            if (!Schema::hasColumn('patient_visits', 'attended_by')) {
                $table->foreignId('attended_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::table('patient_visits', function (Blueprint $table) {
            // Drop the columns we added
            $table->dropColumn([
                'history_present_illness',
                'assessment', 
                'treatment_plan',
                'practitioner',
                'department',
                'oxygen_saturation',
                'pulse_rate',
                'reason_for_visit',
                'attended_by'
            ]);
        });
    }
}
