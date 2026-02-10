<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeServiceIdNullableInServiceResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_results', function (Blueprint $table) {
            // Drop existing foreign key constraint if it exists
            if (Schema::hasColumn('service_results', 'service_id')) {
                $table->dropForeign(['service_id']);
                
                // Make service_id nullable
                $table->unsignedBigInteger('service_id')->nullable()->change();
                
                // Re-add foreign key constraint with nullable
                $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
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
        Schema::table('service_results', function (Blueprint $table) {
            //
        });
    }
}
