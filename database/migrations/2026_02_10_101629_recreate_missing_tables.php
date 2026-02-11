<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateMissingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create patients table (no dependencies)
        if (!Schema::hasTable('patients')) {
            Schema::create('patients', function (Blueprint $table) {
                $table->id();
                $table->string('patient_code')->unique();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('country')->nullable();
                $table->string('emergency_contact_name')->nullable();
                $table->string('emergency_contact_phone')->nullable();
                $table->text('medical_history')->nullable();
                $table->text('allergies')->nullable();
                $table->text('current_medications')->nullable();
                $table->decimal('height', 5, 2)->nullable();
                $table->decimal('weight', 5, 2)->nullable();
                $table->string('blood_type')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
                
                $table->index('patient_code');
                $table->index(['first_name', 'last_name']);
            });
        }

        // Create patient_visits table (depends on patients)
        if (!Schema::hasTable('patient_visits')) {
            Schema::create('patient_visits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->date('visit_date');
                $table->time('visit_time');
                $table->enum('visit_type', ['consultation', 'followup', 'emergency', 'routine'])->default('consultation');
                $table->text('chief_complaint')->nullable();
                $table->text('symptoms')->nullable();
                $table->text('diagnosis')->nullable();
                $table->text('treatment')->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
                $table->decimal('height', 5, 2)->nullable();
                $table->decimal('weight', 5, 2)->nullable();
                $table->decimal('bmi', 5, 2)->nullable();
                $table->string('blood_pressure')->nullable();
                $table->integer('heart_rate')->nullable();
                $table->integer('respiratory_rate')->nullable();
                $table->decimal('temperature', 5, 2)->nullable();
                $table->string('payment_status')->default('pending');
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->decimal('balance_due', 10, 2)->default(0);
                $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
                $table->json('selected_services')->nullable();
                $table->json('selected_package')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index(['patient_id', 'visit_date']);
                $table->index('status');
                $table->index('visit_date');
            });
        }

        // Create patient_services table (depends on patients, patient_visits, services)
        if (!Schema::hasTable('patient_services')) {
            Schema::create('patient_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->foreignId('visit_id')->nullable()->constrained('patient_visits')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->decimal('service_price', 10, 2)->default(0);
                $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
                $table->text('notes')->nullable();
                $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('performed_at')->nullable();
                $table->timestamps();
                
                $table->index(['patient_id', 'service_id']);
                $table->index('visit_id');
                $table->index('status');
            });
        }

        // Create patient_packages table (depends on patients, patient_visits, packages)
        if (!Schema::hasTable('patient_packages')) {
            Schema::create('patient_packages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->foreignId('visit_id')->nullable()->constrained('patient_visits')->onDelete('cascade');
                $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
                $table->decimal('package_price', 10, 2)->default(0);
                $table->enum('status', ['active', 'completed', 'expired', 'cancelled'])->default('active');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('sessions_used')->default(0);
                $table->integer('total_sessions')->default(1);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['patient_id', 'package_id']);
                $table->index('visit_id');
                $table->index('status');
            });
        }

        // Create service_results table (depends on patients, patient_visits, patient_services, patient_packages)
        if (!Schema::hasTable('service_results')) {
            Schema::create('service_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->foreignId('visit_id')->nullable()->constrained('patient_visits')->onDelete('cascade');
                $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
                $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
                $table->foreignId('patient_service_id')->nullable()->constrained('patient_services')->onDelete('set null');
                $table->foreignId('patient_package_id')->nullable()->constrained('patient_packages')->onDelete('set null');
                $table->enum('result_type', ['text', 'numeric', 'file']);
                $table->text('result_text')->nullable();
                $table->decimal('result_numeric', 10, 2)->nullable();
                $table->string('result_file_path')->nullable();
                $table->string('result_file_name')->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected'])->default('draft');
                $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->text('approval_notes')->nullable();
                $table->timestamp('recorded_at')->nullable();
                $table->timestamps();
                
                $table->index(['patient_id', 'service_id']);
                $table->index(['status', 'result_type']);
                $table->index('package_id');
            });
        }

        // Create bills table (depends on patients, patient_visits)
        if (!Schema::hasTable('bills')) {
            Schema::create('bills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->foreignId('visit_id')->nullable()->constrained('patient_visits')->onDelete('cascade');
                $table->string('bill_number')->unique();
                $table->enum('bill_type', ['service', 'package', 'consultation'])->default('service');
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->decimal('amount_paid', 10, 2)->default(0);
                $table->decimal('balance', 10, 2)->default(0);
                $table->enum('status', ['pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending');
                $table->date('due_date')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                $table->index('patient_id');
                $table->index('visit_id');
                $table->index('status');
                $table->index('due_date');
            });
        }

        // Create bill_items table (depends on bills, services, packages)
        if (!Schema::hasTable('bill_items')) {
            Schema::create('bill_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
                $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');
                $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
                $table->string('description');
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->decimal('total_price', 10, 2)->default(0);
                $table->enum('item_type', ['service', 'package'])->default('service');
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->index(['bill_id', 'item_type']);
                $table->index(['service_id', 'bill_id']);
                $table->index(['package_id', 'bill_id']);
            });
        }

        // Create payments table (depends on bills, patients)
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'mobile_money', 'insurance'])->default('cash');
                $table->string('transaction_id')->nullable();
                $table->date('payment_date');
                $table->text('notes')->nullable();
                $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
                $table->timestamps();
                
                $table->index('bill_id');
                $table->index('patient_id');
                $table->index('payment_date');
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
        Schema::dropIfExists('payments');
        Schema::dropIfExists('patient_packages');
        Schema::dropIfExists('patient_services');
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('service_results');
        Schema::dropIfExists('patient_visits');
        Schema::dropIfExists('patients');
    }
}
