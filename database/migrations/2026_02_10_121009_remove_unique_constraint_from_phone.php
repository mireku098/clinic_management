<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueConstraintFromPhone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Drop unique index from phone column if it exists
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('patients');
            
            if (isset($indexes['patients_phone_unique'])) {
                $table->dropUnique('patients_phone_unique');
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
        Schema::table('patients', function (Blueprint $table) {
            // Add unique constraint back to phone column
            $table->unique('phone', 'patients_phone_unique');
        });
    }
}
