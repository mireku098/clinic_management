<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToServiceResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_results', function (Blueprint $table) {
            if (!Schema::hasColumn('service_results', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('service_results', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
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
            if (Schema::hasColumn('service_results', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('service_results', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
}
