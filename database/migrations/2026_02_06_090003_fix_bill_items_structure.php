<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixBillItemsStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_items', function (Blueprint $table) {
            // Drop the existing item_id column
            $table->dropColumn('item_id');
            
            // Add service_id and package_id columns
            $table->foreignId('service_id')->nullable()->after('bill_id');
            $table->foreignId('package_id')->nullable()->after('service_id');
            $table->text('notes')->nullable()->after('total_price');
            
            // Add indexes
            $table->index(['bill_id', 'service_id']);
            $table->index(['bill_id', 'package_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['package_id']);
            $table->dropColumn(['service_id', 'package_id', 'notes']);
            $table->dropIndex(['bill_id', 'service_id']);
            $table->dropIndex(['bill_id', 'package_id']);
            
            // Restore item_id column
            $table->unsignedBigInteger('item_id')->after('bill_id');
        });
    }
}
