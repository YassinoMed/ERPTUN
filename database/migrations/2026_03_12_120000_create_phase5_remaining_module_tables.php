<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('partner_code');
            $table->string('name');
            $table->string('partner_type')->default('reseller');
            $table->string('status')->default('active');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('vender_id')->nullable()->constrained('venders')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
        });

        Schema::create('vendor_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vender_id')->constrained('venders')->cascadeOnDelete();
            $table->string('period_label');
            $table->decimal('quality_score', 8, 2)->default(0);
            $table->decimal('delivery_score', 8, 2)->default(0);
            $table->decimal('cost_score', 8, 2)->default(0);
            $table->decimal('service_score', 8, 2)->default(0);
            $table->decimal('total_score', 8, 2)->default(0);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
        });

        Schema::create('product_lifecycle_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_service_id')->constrained('product_services')->cascadeOnDelete();
            $table->string('stage');
            $table->string('status')->default('planned');
            $table->date('effective_date')->nullable();
            $table->foreignId('owner_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('compliance_status')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
        });

        Schema::create('lims_records', function (Blueprint $table) {
            $table->id();
            $table->string('sample_code');
            $table->foreignId('product_service_id')->nullable()->constrained('product_services')->nullOnDelete();
            $table->string('lot_reference')->nullable();
            $table->string('test_type');
            $table->string('status')->default('scheduled');
            $table->text('result_summary')->nullable();
            $table->timestamp('tested_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
        });

        Schema::create('hse_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('incident_code');
            $table->string('title');
            $table->string('category');
            $table->string('severity')->default('medium');
            $table->string('status')->default('open');
            $table->date('occurred_on')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('reported_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('actions')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
        });

        Schema::create('succession_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('successor_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('readiness_level')->default('medium');
            $table->string('risk_level')->default('medium');
            $table->date('target_date')->nullable();
            $table->string('status')->default('planned');
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
        });

        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('ticket_code');
            $table->string('attendee_name');
            $table->string('attendee_email')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->string('status')->default('reserved');
            $table->timestamp('checked_in_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
        });

        Schema::create('microfinance_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('loan_number');
            $table->decimal('principal_amount', 15, 2)->default(0);
            $table->decimal('interest_rate', 8, 2)->default(0);
            $table->decimal('installment_amount', 15, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->string('status')->default('draft');
            $table->string('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
        });

        Schema::create('leasing_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('contract_number');
            $table->string('asset_name');
            $table->decimal('lease_amount', 15, 2)->default(0);
            $table->decimal('residual_amount', 15, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('payment_frequency')->nullable();
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
        });

        Schema::create('transport_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('origin');
            $table->string('destination');
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->date('departure_date')->nullable();
            $table->date('arrival_date')->nullable();
            $table->string('status')->default('planned');
            $table->decimal('freight_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
            $table->index(['created_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_shipments');
        Schema::dropIfExists('leasing_contracts');
        Schema::dropIfExists('microfinance_loans');
        Schema::dropIfExists('event_tickets');
        Schema::dropIfExists('succession_plans');
        Schema::dropIfExists('hse_incidents');
        Schema::dropIfExists('lims_records');
        Schema::dropIfExists('product_lifecycle_records');
        Schema::dropIfExists('vendor_ratings');
        Schema::dropIfExists('partners');
    }
};
