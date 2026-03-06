<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agri_lots', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100);
            $table->string('name', 191);
            $table->string('crop_type', 191);
            $table->date('harvest_date')->nullable();
            $table->decimal('quantity', 18, 3)->default(0);
            $table->string('unit', 32)->default('kg');
            $table->string('status', 32)->default('active');
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->unique(['code', 'created_by']);
        });

        Schema::create('agri_trace_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lot_id');
            $table->string('step', 191);
            $table->string('location', 191)->nullable();
            $table->string('actor', 191)->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('occurred_at');
            $table->string('previous_hash', 64)->nullable();
            $table->string('current_hash', 64);
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->foreign('lot_id')->references('id')->on('agri_lots')->onDelete('cascade');
        });

        Schema::create('agri_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lot_id');
            $table->string('certificate_number', 191);
            $table->dateTime('issued_at');
            $table->string('verification_hash', 64);
            $table->text('qr_payload')->nullable();
            $table->string('status', 32)->default('issued');
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->unique(['certificate_number', 'created_by']);
            $table->foreign('lot_id')->references('id')->on('agri_lots')->onDelete('cascade');
        });

        Schema::create('agri_parcels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 100);
            $table->decimal('area', 12, 2)->default(0);
            $table->string('area_unit', 16)->default('ha');
            $table->string('soil_type', 100)->nullable();
            $table->string('location', 191)->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->unique(['code', 'created_by']);
        });

        Schema::create('agri_crop_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parcel_id');
            $table->string('crop_name', 191);
            $table->string('variety', 191)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 32)->default('planned');
            $table->decimal('expected_yield', 18, 3)->default(0);
            $table->string('yield_unit', 16)->default('kg');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->foreign('parcel_id')->references('id')->on('agri_parcels')->onDelete('cascade');
        });

        Schema::create('agri_rotation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('crop_name', 191);
            $table->string('follow_crop_name', 191)->nullable();
            $table->unsignedInteger('min_gap_days')->default(0);
            $table->text('recommendation')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
        });

        Schema::create('agri_weather_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parcel_id')->nullable();
            $table->string('alert_type', 64);
            $table->string('severity', 32)->default('medium');
            $table->text('message');
            $table->dateTime('alert_date');
            $table->dateTime('acknowledged_at')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->foreign('parcel_id')->references('id')->on('agri_parcels')->onDelete('cascade');
        });

        Schema::create('agri_cooperatives', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 100);
            $table->string('region', 191)->nullable();
            $table->string('currency', 8)->default('USD');
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->unique(['code', 'created_by']);
        });

        Schema::create('agri_coop_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperative_id');
            $table->string('name', 191);
            $table->string('member_code', 100);
            $table->decimal('share_percent', 5, 2)->default(0);
            $table->decimal('advance_balance', 18, 2)->default(0);
            $table->string('contact_phone', 50)->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->unique(['member_code', 'created_by']);
            $table->foreign('cooperative_id')->references('id')->on('agri_cooperatives')->onDelete('cascade');
        });

        Schema::create('agri_harvest_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperative_id');
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('lot_id')->nullable();
            $table->string('crop_type', 191);
            $table->decimal('quantity', 18, 3)->default(0);
            $table->string('unit', 16)->default('kg');
            $table->date('delivery_date');
            $table->decimal('price_per_unit', 18, 2)->default(0);
            $table->decimal('total_value', 18, 2)->default(0);
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->foreign('cooperative_id')->references('id')->on('agri_cooperatives')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('agri_coop_members')->onDelete('cascade');
            $table->foreign('lot_id')->references('id')->on('agri_lots')->onDelete('set null');
        });

        Schema::create('agri_revenue_distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperative_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_revenue', 18, 2)->default(0);
            $table->string('distribution_method', 32)->default('quantity');
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->foreign('cooperative_id')->references('id')->on('agri_cooperatives')->onDelete('cascade');
        });

        Schema::create('agri_member_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distribution_id');
            $table->unsignedBigInteger('member_id');
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('advance_deducted', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->foreign('distribution_id')->references('id')->on('agri_revenue_distributions')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('agri_coop_members')->onDelete('cascade');
        });

        Schema::create('agri_purchase_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperative_id')->nullable();
            $table->string('contract_number', 191);
            $table->string('buyer_name', 191);
            $table->string('crop_type', 191);
            $table->decimal('quantity', 18, 3)->default(0);
            $table->string('unit', 16)->default('kg');
            $table->decimal('fixed_price', 18, 2)->default(0);
            $table->string('price_currency', 8)->default('USD');
            $table->date('delivery_start');
            $table->date('delivery_end');
            $table->string('status', 32)->default('active');
            $table->decimal('hedge_ratio', 5, 2)->default(0);
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->unique(['contract_number', 'created_by']);
            $table->foreign('cooperative_id')->references('id')->on('agri_cooperatives')->onDelete('set null');
        });

        Schema::create('agri_hedge_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->string('instrument', 64);
            $table->string('position_type', 32)->default('future');
            $table->decimal('quantity', 18, 3)->default(0);
            $table->decimal('price', 18, 2)->default(0);
            $table->date('opened_at');
            $table->date('closed_at')->nullable();
            $table->string('status', 32)->default('open');
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->foreign('contract_id')->references('id')->on('agri_purchase_contracts')->onDelete('cascade');
        });

        Schema::create('agri_price_indices', function (Blueprint $table) {
            $table->id();
            $table->string('crop_type', 191);
            $table->string('market', 64)->nullable();
            $table->decimal('price', 18, 2)->default(0);
            $table->string('currency', 8)->default('USD');
            $table->dateTime('recorded_at');
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agri_price_indices');
        Schema::dropIfExists('agri_hedge_positions');
        Schema::dropIfExists('agri_purchase_contracts');
        Schema::dropIfExists('agri_member_payouts');
        Schema::dropIfExists('agri_revenue_distributions');
        Schema::dropIfExists('agri_harvest_deliveries');
        Schema::dropIfExists('agri_coop_members');
        Schema::dropIfExists('agri_cooperatives');
        Schema::dropIfExists('agri_weather_alerts');
        Schema::dropIfExists('agri_rotation_rules');
        Schema::dropIfExists('agri_crop_plans');
        Schema::dropIfExists('agri_parcels');
        Schema::dropIfExists('agri_certificates');
        Schema::dropIfExists('agri_trace_events');
        Schema::dropIfExists('agri_lots');
    }
};
