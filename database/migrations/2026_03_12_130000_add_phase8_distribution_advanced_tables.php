<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('retail_stores', function (Blueprint $table) {
            if (!Schema::hasColumn('retail_stores', 'store_type')) {
                $table->string('store_type', 32)->default('store')->after('code');
            }
            if (!Schema::hasColumn('retail_stores', 'parent_store_id')) {
                $table->unsignedBigInteger('parent_store_id')->nullable()->after('manager_name');
            }
            if (!Schema::hasColumn('retail_stores', 'warehouse_id')) {
                $table->unsignedBigInteger('warehouse_id')->nullable()->after('parent_store_id');
            }
            if (!Schema::hasColumn('retail_stores', 'target_revenue')) {
                $table->decimal('target_revenue', 16, 2)->default(0)->after('warehouse_id');
            }
            if (!Schema::hasColumn('retail_stores', 'target_margin')) {
                $table->decimal('target_margin', 8, 2)->default(0)->after('target_revenue');
            }
        });

        Schema::table('pos_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_sessions', 'transactions_count')) {
                $table->integer('transactions_count')->default(0)->after('variance_amount');
            }
            if (!Schema::hasColumn('pos_sessions', 'mixed_payment_enabled')) {
                $table->boolean('mixed_payment_enabled')->default(true)->after('transactions_count');
            }
            if (!Schema::hasColumn('pos_sessions', 'session_mode')) {
                $table->string('session_mode', 32)->default('counter')->after('mixed_payment_enabled');
            }
        });

        Schema::table('retail_promotions', function (Blueprint $table) {
            if (!Schema::hasColumn('retail_promotions', 'retail_store_id')) {
                $table->unsignedBigInteger('retail_store_id')->nullable()->after('scope_type');
            }
            if (!Schema::hasColumn('retail_promotions', 'audience_type')) {
                $table->string('audience_type', 32)->default('all')->after('retail_store_id');
            }
            if (!Schema::hasColumn('retail_promotions', 'auto_apply')) {
                $table->boolean('auto_apply')->default(false)->after('audience_type');
            }
            if (!Schema::hasColumn('retail_promotions', 'budget_amount')) {
                $table->decimal('budget_amount', 16, 2)->default(0)->after('minimum_amount');
            }
        });

        Schema::table('commercial_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('commercial_contracts', 'retail_store_id')) {
                $table->unsignedBigInteger('retail_store_id')->nullable()->after('party_id');
            }
            if (!Schema::hasColumn('commercial_contracts', 'category')) {
                $table->string('category', 64)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('commercial_contracts', 'owner_name')) {
                $table->string('owner_name')->nullable()->after('category');
            }
            if (!Schema::hasColumn('commercial_contracts', 'renewal_notice_days')) {
                $table->integer('renewal_notice_days')->default(30)->after('billing_cycle');
            }
        });

        if (!Schema::hasTable('retail_procurement_requests')) {
            Schema::create('retail_procurement_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('retail_store_id')->nullable();
                $table->unsignedBigInteger('vender_id')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->string('reference', 100);
                $table->string('title');
                $table->decimal('budget_amount', 16, 2)->default(0);
                $table->date('needed_by')->nullable();
                $table->string('status', 32)->default('draft');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->index(['created_by', 'status']);
            });
        }

        if (!Schema::hasTable('store_replenishment_requests')) {
            Schema::create('store_replenishment_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('source_store_id')->nullable();
                $table->unsignedBigInteger('destination_store_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->decimal('suggested_quantity', 16, 3)->default(0);
                $table->decimal('approved_quantity', 16, 3)->default(0);
                $table->date('needed_by')->nullable();
                $table->string('status', 32)->default('draft');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();
                $table->index(['created_by', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('store_replenishment_requests');
        Schema::dropIfExists('retail_procurement_requests');
    }
};
