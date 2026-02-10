<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackageServiceFieldsToPatientVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_visits', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('selected_services')->nullable();
            $table->text('selected_package')->nullable();
            $table->string('payment_status')->default('pending');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2)->default(0);
            
            $table->index(['package_id']);
            $table->index(['payment_status']);
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
            $table->dropColumn([
                'package_id',
                'total_amount',
                'selected_services',
                'selected_package',
                'payment_status',
                'amount_paid',
                'balance_due'
            ]);
        });
    }
}
