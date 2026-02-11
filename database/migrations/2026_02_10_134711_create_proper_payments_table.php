<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProperPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop existing incomplete payments table and recreate
        Schema::dropIfExists('payments');
        
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
            $table->decimal('amount_before', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('balance_after', 10, 2)->default(0);
            $table->string('payment_method', 50);
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('payment_date');
            $table->dateTime('payment_time');
            $table->timestamps();
            
            $table->index(['patient_id', 'bill_id']);
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
