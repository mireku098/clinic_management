<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemainingMissingPatientColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Add missing columns from the SQL error
            if (!Schema::hasColumn('patients', 'occupation')) {
                $table->string('occupation')->nullable();
            }
            if (!Schema::hasColumn('patients', 'height')) {
                $table->decimal('height', 5, 2)->nullable(); // for height in cm
            }
            if (!Schema::hasColumn('patients', 'allergies')) {
                $table->text('allergies')->nullable();
            }
            if (!Schema::hasColumn('patients', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable();
            }
            if (!Schema::hasColumn('patients', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone', 20)->nullable();
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
        Schema::table('patients', function (Blueprint $table) {
            // Drop the columns we added
            $table->dropColumn([
                'occupation',
                'height',
                'allergies',
                'emergency_contact_name',
                'emergency_contact_phone'
            ]);
        });
    }
}
