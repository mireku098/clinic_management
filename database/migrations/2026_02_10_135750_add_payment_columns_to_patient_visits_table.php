<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentColumnsToPatientVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            // Add payment tracking columns
            if (!Schema::hasColumn('patient_visits', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'partial', 'paid', 'cancelled'])->default('pending')->after('notes');
            }
            if (!Schema::hasColumn('patient_visits', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0)->after('payment_status');
            }
            if (!Schema::hasColumn('patient_visits', 'balance_due')) {
                $table->decimal('balance_due', 10, 2)->default(0)->after('amount_paid');
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
            // Drop the payment tracking columns
            if (Schema::hasColumn('patient_visits', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('patient_visits', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }
            if (Schema::hasColumn('patient_visits', 'balance_due')) {
                $table->dropColumn('balance_due');
            }
        });
    }
}
