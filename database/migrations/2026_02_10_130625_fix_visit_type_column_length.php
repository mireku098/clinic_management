<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixVisitTypeColumnLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            // Fix visit_type column length to accommodate values like 'walk-in'
            $table->string('visit_type', 50)->change();
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
            // Revert visit_type column to shorter length
            $table->string('visit_type', 20)->change();
        });
    }
}
