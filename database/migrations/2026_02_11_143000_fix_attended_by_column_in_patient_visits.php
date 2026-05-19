<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixAttendedByColumnInPatientVisits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign('patient_visits_attended_by_foreign');
            
            // Then drop the column
            $table->dropColumn('attended_by');
        });
        
        Schema::table('patient_visits', function (Blueprint $table) {
            // Add the column back as a string
            $table->string('attended_by')->nullable()->after('notes');
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
            // Drop the string column
            $table->dropColumn('attended_by');
        });
        
        Schema::table('patient_visits', function (Blueprint $table) {
            // Add back the foreign key column
            $table->foreignId('attended_by')->nullable()->after('notes')->constrained('users')->onDelete('set null');
        });
    }
}
