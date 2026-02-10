<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            // Add missing fields from UI
            $table->string('practitioner')->nullable()->after('visit_type');
            $table->string('department')->nullable()->after('practitioner');
            
            // Add missing vital signs
            $table->decimal('height', 5, 2)->nullable()->after('weight'); // cm, max 999.99
            $table->integer('heart_rate')->nullable()->after('blood_pressure'); // bpm
            $table->integer('oxygen_saturation')->nullable()->after('heart_rate'); // percentage
            $table->integer('respiratory_rate')->nullable()->after('oxygen_saturation'); // breaths per minute
            $table->decimal('bmi', 5, 2)->nullable()->after('respiratory_rate'); // calculated
            
            // Add missing clinical notes
            $table->text('chief_complaint')->nullable()->after('patient_id');
            $table->text('history_present_illness')->nullable()->after('chief_complaint');
            $table->text('assessment')->nullable()->after('history_present_illness');
            $table->text('treatment_plan')->nullable()->after('assessment');
        });
        
        // Migrate data from old fields to new fields
        try {
            \DB::statement('UPDATE patient_visits SET heart_rate = pulse_rate WHERE pulse_rate IS NOT NULL');
            \DB::statement('UPDATE patient_visits SET chief_complaint = reason_for_visit WHERE reason_for_visit IS NOT NULL');
            \DB::statement('UPDATE patient_visits SET treatment_plan = notes WHERE notes IS NOT NULL');
        } catch (\Exception $e) {
            // Ignore migration errors if old columns don't exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            // Drop new fields
            $table->dropColumn([
                'practitioner', 'department', 'height', 'heart_rate', 
                'oxygen_saturation', 'respiratory_rate', 'bmi',
                'chief_complaint', 'history_present_illness', 
                'assessment', 'treatment_plan'
            ]);
        });
    }
};
