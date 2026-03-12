<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->unsignedInteger('base_capacity')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('hotel_room_types')->cascadeOnDelete();
            $table->string('name');
            $table->string('floor')->nullable();
            $table->string('status')->default('available');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->json('credentials')->nullable();
            $table->string('sync_status')->default('idle');
            $table->timestamp('last_synced_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_rate_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('hotel_room_types')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('base_rate', 12, 2)->default(0);
            $table->decimal('min_rate', 12, 2)->default(0);
            $table->decimal('max_rate', 12, 2)->default(0);
            $table->string('currency')->default('EUR');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_channel_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('hotel_channels')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('hotel_room_types')->cascadeOnDelete();
            $table->foreignId('rate_plan_id')->constrained('hotel_rate_plans')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('rate', 12, 2);
            $table->unsignedInteger('min_stay')->default(1);
            $table->unsignedInteger('max_stay')->nullable();
            $table->boolean('closed_to_arrival')->default(false);
            $table->boolean('closed_to_departure')->default(false);
            $table->timestamps();
        });

        Schema::create('hotel_channel_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('hotel_channels')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('hotel_room_types')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('available')->default(0);
            $table->boolean('stop_sell')->default(false);
            $table->timestamps();
        });

        Schema::create('hotel_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->nullable()->constrained('hotel_channels')->nullOnDelete();
            $table->foreignId('room_type_id')->nullable()->constrained('hotel_room_types')->nullOnDelete();
            $table->string('external_reservation_id')->nullable();
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->date('check_in');
            $table->date('check_out');
            $table->string('status')->default('confirmed');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('currency')->default('EUR');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_channel_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->nullable()->constrained('hotel_channels')->nullOnDelete();
            $table->string('status')->default('success');
            $table->string('direction')->default('outbound');
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('hotel_room_types')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('demand_score', 6, 2)->default(0);
            $table->decimal('occupancy_rate', 6, 2)->default(0);
            $table->decimal('seasonal_factor', 6, 2)->default(1);
            $table->decimal('event_factor', 6, 2)->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('room_type_id')->nullable()->constrained('hotel_room_types')->nullOnDelete();
            $table->string('customer_segment')->nullable();
            $table->decimal('min_rate', 12, 2)->default(0);
            $table->decimal('max_rate', 12, 2)->default(0);
            $table->decimal('margin', 6, 2)->default(0);
            $table->decimal('occupancy_threshold', 6, 2)->default(0);
            $table->decimal('seasonality_multiplier', 6, 2)->default(1);
            $table->decimal('event_multiplier', 6, 2)->default(1);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_price_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('hotel_room_types')->cascadeOnDelete();
            $table->foreignId('rate_plan_id')->constrained('hotel_rate_plans')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('recommended_rate', 12, 2)->default(0);
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_housekeeping_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('hotel_rooms')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('priority')->default('normal');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_housekeeping_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('room_type_id')->nullable()->constrained('hotel_room_types')->nullOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_housekeeping_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')->constrained('hotel_housekeeping_checklists')->cascadeOnDelete();
            $table->string('title');
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        Schema::create('hotel_housekeeping_task_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('hotel_housekeeping_tasks')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained('hotel_housekeeping_checklist_items')->cascadeOnDelete();
            $table->boolean('is_done')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('unit')->default('unit');
            $table->decimal('quantity_on_hand', 12, 2)->default(0);
            $table->decimal('reorder_level', 12, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('hotel_inventory_items')->cascadeOnDelete();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('type')->default('issue');
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_maintenance_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('hotel_rooms')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('open');
            $table->string('priority')->default('normal');
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_upsell_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency')->default('EUR');
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_upsell_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_upsell_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('hotel_upsell_packages')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('hotel_upsell_services')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('hotel_upsell_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->nullable()->constrained('hotel_reservations')->nullOnDelete();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreignId('room_type_id')->nullable()->constrained('hotel_room_types')->nullOnDelete();
            $table->unsignedInteger('stay_length_min')->nullable();
            $table->unsignedInteger('stay_length_max')->nullable();
            $table->json('segments')->nullable();
            $table->json('offer_payload')->nullable();
            $table->string('status')->default('proposed');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_upsell_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('hotel_upsell_offers')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('hotel_upsell_services')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamp('converted_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_upsell_conversions');
        Schema::dropIfExists('hotel_upsell_offers');
        Schema::dropIfExists('hotel_upsell_package_items');
        Schema::dropIfExists('hotel_upsell_packages');
        Schema::dropIfExists('hotel_upsell_services');
        Schema::dropIfExists('hotel_maintenance_issues');
        Schema::dropIfExists('hotel_inventory_movements');
        Schema::dropIfExists('hotel_inventory_items');
        Schema::dropIfExists('hotel_housekeeping_task_items');
        Schema::dropIfExists('hotel_housekeeping_checklist_items');
        Schema::dropIfExists('hotel_housekeeping_checklists');
        Schema::dropIfExists('hotel_housekeeping_tasks');
        Schema::dropIfExists('hotel_price_recommendations');
        Schema::dropIfExists('hotel_pricing_rules');
        Schema::dropIfExists('hotel_demand_forecasts');
        Schema::dropIfExists('hotel_channel_sync_logs');
        Schema::dropIfExists('hotel_reservations');
        Schema::dropIfExists('hotel_channel_availabilities');
        Schema::dropIfExists('hotel_channel_rates');
        Schema::dropIfExists('hotel_rate_plans');
        Schema::dropIfExists('hotel_channels');
        Schema::dropIfExists('hotel_rooms');
        Schema::dropIfExists('hotel_room_types');
    }
};
