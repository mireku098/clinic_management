<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageServicesTableIfNotExists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('package_services')) {
            Schema::create('package_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->string('service_name')->nullable();
                $table->integer('sessions')->default(1);
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('service_total', 10, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['package_id', 'service_id']);
                $table->unique(['package_id', 'service_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_services');
    }
}
