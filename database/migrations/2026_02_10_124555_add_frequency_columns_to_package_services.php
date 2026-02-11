<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFrequencyColumnsToPackageServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_services', function (Blueprint $table) {
            // Add missing frequency columns
            if (!Schema::hasColumn('package_services', 'frequency_type')) {
                $table->string('frequency_type')->default('once');
            }
            if (!Schema::hasColumn('package_services', 'frequency_value')) {
                $table->integer('frequency_value')->default(1);
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
        Schema::table('package_services', function (Blueprint $table) {
            // Drop the frequency columns
            $table->dropColumn(['frequency_type', 'frequency_value']);
        });
    }
}
