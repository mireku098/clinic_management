<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackageToServiceResultsResultType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_results', function (Blueprint $table) {
            // Drop existing result_type column
            $table->dropColumn('result_type');
        });
        
        Schema::table('service_results', function (Blueprint $table) {
            // Recreate result_type column with package included
            $table->enum('result_type', ['text', 'numeric', 'file', 'package'])->default('text')->after('patient_package_id');
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
            // Revert to original ENUM values
            $table->dropColumn('result_type');
        });
        
        Schema::table('service_results', function (Blueprint $table) {
            $table->enum('result_type', ['text', 'numeric', 'file'])->default('text')->after('patient_package_id');
        });
    }
}
