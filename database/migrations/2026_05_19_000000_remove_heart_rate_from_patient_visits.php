<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveHeartRateFromPatientVisits extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('patient_visits', 'heart_rate')) {
            return;
        }

        DB::statement('UPDATE patient_visits SET pulse_rate = COALESCE(pulse_rate, heart_rate) WHERE heart_rate IS NOT NULL');

        Schema::table('patient_visits', function (Blueprint $table) {
            $table->dropColumn('heart_rate');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('patient_visits', 'heart_rate')) {
            return;
        }

        Schema::table('patient_visits', function (Blueprint $table) {
            $table->integer('heart_rate')->nullable()->after('blood_pressure');
        });

        DB::statement('UPDATE patient_visits SET heart_rate = pulse_rate WHERE pulse_rate IS NOT NULL');
    }
}
