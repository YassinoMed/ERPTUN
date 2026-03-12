<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('retail_stores')) {
            Schema::create('retail_stores', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 100);
                $table->string('region')->nullable();
                $table->string('manager_name')->nullable();
                $table->string('status', 32)->default('active');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->unique(['code', 'created_by']);
            });
        }

        if (!Schema::hasTable('pos_sessions')) {
            Schema::create('pos_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cash_register_id')->nullable();
                $table->unsignedBigInteger('retail_store_id')->nullable();
                $table->unsignedBigInteger('opened_by')->nullable();
                $table->dateTime('opened_at');
                $table->dateTime('closed_at')->nullable();
                $table->decimal('expected_amount', 16, 2)->default(0);
                $table->decimal('actual_amount', 16, 2)->default(0);
                $table->decimal('variance_amount', 16, 2)->default(0);
                $table->string('status', 32)->default('open');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->index(['created_by', 'status']);
            });
        }

        if (!Schema::hasTable('retail_promotions')) {
            Schema::create('retail_promotions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 100);
                $table->string('promotion_type', 64)->default('discount');
                $table->string('scope_type', 64)->default('global');
                $table->decimal('discount_value', 16, 2)->default(0);
                $table->decimal('minimum_amount', 16, 2)->default(0);
                $table->dateTime('starts_at')->nullable();
                $table->dateTime('ends_at')->nullable();
                $table->string('status', 32)->default('draft');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->unique(['code', 'created_by']);
            });
        }

        if (!Schema::hasTable('commercial_contracts')) {
            Schema::create('commercial_contracts', function (Blueprint $table) {
                $table->id();
                $table->string('contract_number', 100);
                $table->string('title');
                $table->string('party_type', 32)->default('customer');
                $table->unsignedBigInteger('party_id')->nullable();
                $table->decimal('amount', 16, 2)->default(0);
                $table->string('billing_cycle', 32)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('status', 32)->default('draft');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->unique(['contract_number', 'created_by']);
            });
        }

        if (!Schema::hasTable('lab_orders')) {
            Schema::create('lab_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('consultation_id')->nullable();
                $table->string('panel_name');
                $table->string('sample_type', 64)->nullable();
                $table->string('status', 32)->default('ordered');
                $table->boolean('critical_flag')->default(false);
                $table->dateTime('ordered_at');
                $table->dateTime('collected_at')->nullable();
                $table->dateTime('validated_at')->nullable();
                $table->text('result_summary')->nullable();
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->index(['created_by', 'status']);
            });
        }

        if (!Schema::hasTable('surgical_procedures')) {
            Schema::create('surgical_procedures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('hospital_admission_id')->nullable();
                $table->string('procedure_name');
                $table->string('surgeon_name')->nullable();
                $table->string('theatre_name')->nullable();
                $table->dateTime('scheduled_at');
                $table->string('status', 32)->default('planned');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->index(['created_by', 'status']);
            });
        }

        if (!Schema::hasTable('biomedical_assets')) {
            Schema::create('biomedical_assets', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('asset_code', 100);
                $table->string('equipment_type', 100)->nullable();
                $table->string('serial_number', 100)->nullable();
                $table->string('location')->nullable();
                $table->date('calibration_due_date')->nullable();
                $table->string('maintenance_status', 32)->default('operational');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->unique(['asset_code', 'created_by']);
            });
        }

        if (!Schema::hasTable('medical_specialties')) {
            Schema::create('medical_specialties', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 100);
                $table->string('department_name')->nullable();
                $table->string('head_name')->nullable();
                $table->string('status', 32)->default('active');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->unique(['code', 'created_by']);
            });
        }

        if (!Schema::hasTable('patient_portal_messages')) {
            Schema::create('patient_portal_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id');
                $table->string('direction', 16)->default('outbound');
                $table->string('subject');
                $table->text('message');
                $table->dateTime('sent_at');
                $table->string('status', 32)->default('sent');
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->index(['created_by', 'patient_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_portal_messages');
        Schema::dropIfExists('medical_specialties');
        Schema::dropIfExists('biomedical_assets');
        Schema::dropIfExists('surgical_procedures');
        Schema::dropIfExists('lab_orders');
        Schema::dropIfExists('commercial_contracts');
        Schema::dropIfExists('retail_promotions');
        Schema::dropIfExists('pos_sessions');
        Schema::dropIfExists('retail_stores');
    }
};
