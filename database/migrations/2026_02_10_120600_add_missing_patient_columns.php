<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingPatientColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Add missing columns from original migration
            if (!Schema::hasColumn('patients', 'age')) {
                $table->unsignedTinyInteger('age')->nullable();
            }
            if (!Schema::hasColumn('patients', 'marital_status')) {
                $table->string('marital_status')->nullable();
            }
            if (!Schema::hasColumn('patients', 'blood_group')) {
                $table->string('blood_group', 3)->nullable();
            }
            if (!Schema::hasColumn('patients', 'sickle_cell_status')) {
                $table->string('sickle_cell_status', 10)->nullable();
            }
            if (!Schema::hasColumn('patients', 'chronic_conditions')) {
                $table->text('chronic_conditions')->nullable();
            }
            if (!Schema::hasColumn('patients', 'photo_path')) {
                $table->string('photo_path')->nullable();
            }
            if (!Schema::hasColumn('patients', 'registered_at')) {
                $table->timestamp('registered_at')->nullable();
            }
            
            // Fix phone column to be unique as per original migration
            if (Schema::hasColumn('patients', 'phone')) {
                // We'll handle the unique constraint separately if needed
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
                'age',
                'marital_status',
                'blood_group',
                'sickle_cell_status',
                'chronic_conditions',
                'photo_path',
                'registered_at'
            ]);
        });
    }
}
