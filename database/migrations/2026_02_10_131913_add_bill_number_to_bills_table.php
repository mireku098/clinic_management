<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillNumberToBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            if (!Schema::hasColumn('bills', 'bill_number')) {
                $table->string('bill_number')->unique()->after('id');
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
        Schema::table('bills', function (Blueprint $table) {
            if (Schema::hasColumn('bills', 'bill_number')) {
                $table->dropColumn('bill_number');
            }
        });
    }
}
