<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddScheduledStatusToPatientVisits extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('patient_visits', 'status')) {
            return;
        }

        DB::statement("ALTER TABLE patient_visits MODIFY COLUMN status ENUM('scheduled', 'pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        if (!Schema::hasColumn('patient_visits', 'status')) {
            return;
        }

        DB::table('patient_visits')->where('status', 'scheduled')->update(['status' => 'pending']);
        DB::table('patient_visits')->where('status', 'cancelled')->update(['status' => 'pending']);

        DB::statement("ALTER TABLE patient_visits MODIFY COLUMN status ENUM('pending', 'completed') NOT NULL DEFAULT 'pending'");
    }
}
