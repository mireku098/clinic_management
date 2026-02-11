<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentBalanceColumnsToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add missing balance tracking columns
            if (!Schema::hasColumn('payments', 'amount_before')) {
                $table->decimal('amount_before', 10, 2)->default(0)->after('bill_id');
            }
            if (!Schema::hasColumn('payments', 'balance_after')) {
                $table->decimal('balance_after', 10, 2)->default(0)->after('amount_paid');
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
        Schema::table('payments', function (Blueprint $table) {
            // Drop the balance tracking columns
            if (Schema::hasColumn('payments', 'amount_before')) {
                $table->dropColumn('amount_before');
            }
            if (Schema::hasColumn('payments', 'balance_after')) {
                $table->dropColumn('balance_after');
            }
        });
    }
}
