<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('package_services', function (Blueprint $table) {
            // Add missing columns
            $table->decimal('unit_price', 10, 2)->after('service_id');
            $table->string('frequency_type')->after('unit_price'); // once, per_week, per_month
            $table->integer('frequency_value')->after('frequency_type');
            $table->integer('sessions')->after('frequency_value');
            $table->decimal('service_total', 10, 2)->after('sessions');
            
            // Add indexes for better performance
            $table->index(['package_id', 'service_id']);
            $table->index('frequency_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_services', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'frequency_type', 'frequency_value', 'sessions', 'service_total']);
        });
    }
};
