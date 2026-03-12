<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('industrial_resources')) {
            Schema::create('industrial_resources', function (Blueprint $table) {
                $table->id();
                $table->string('type')->default('site');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('manager_id')->nullable();
                $table->string('code')->nullable();
                $table->string('name');
                $table->string('status')->default('active');
                $table->decimal('capacity_hours_per_day', 16, 2)->default(0);
                $table->integer('capacity_workers')->default(0);
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'type'], 'industrial_resources_creator_type_idx');
            });
        }

        if (!Schema::hasTable('production_routings')) {
            Schema::create('production_routings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('code')->nullable();
                $table->string('name');
                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'product_id'], 'production_routings_creator_product_idx');
            });
        }

        if (!Schema::hasTable('production_routing_steps')) {
            Schema::create('production_routing_steps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('production_routing_id');
                $table->integer('sequence')->default(1);
                $table->string('name');
                $table->unsignedBigInteger('work_center_id')->nullable();
                $table->unsignedBigInteger('industrial_resource_id')->nullable();
                $table->integer('planned_minutes')->default(0);
                $table->decimal('setup_cost', 16, 2)->default(0);
                $table->decimal('run_cost', 16, 2)->default(0);
                $table->decimal('scrap_percent', 5, 2)->default(0);
                $table->boolean('is_subcontracted')->default(false);
                $table->text('instructions')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'production_routing_id'], 'production_routing_steps_creator_routing_idx');
            });
        }

        if (!Schema::hasTable('production_shift_teams')) {
            Schema::create('production_shift_teams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->unsignedBigInteger('supervisor_id')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'status'], 'production_shift_teams_creator_status_idx');
            });
        }

        if (!Schema::hasTable('industrial_subcontract_orders')) {
            Schema::create('industrial_subcontract_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('production_order_id')->nullable();
                $table->unsignedBigInteger('production_routing_step_id')->nullable();
                $table->unsignedBigInteger('vender_id')->nullable();
                $table->string('reference')->nullable();
                $table->string('status')->default('draft');
                $table->decimal('quantity', 16, 4)->default(0);
                $table->decimal('unit_cost', 16, 2)->default(0);
                $table->date('planned_send_date')->nullable();
                $table->date('planned_receive_date')->nullable();
                $table->date('actual_receive_date')->nullable();
                $table->text('quality_notes')->nullable();
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'status'], 'industrial_subcontract_orders_creator_status_idx');
            });
        }

        if (!Schema::hasTable('industrial_quality_plans')) {
            Schema::create('industrial_quality_plans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('production_routing_id')->nullable();
                $table->string('name');
                $table->string('check_stage')->default('in_process');
                $table->string('sampling_rule')->nullable();
                $table->string('status')->default('active');
                $table->text('acceptance_criteria')->nullable();
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'check_stage'], 'industrial_quality_plans_creator_stage_idx');
            });
        }

        if (!Schema::hasTable('industrial_maintenance_orders')) {
            Schema::create('industrial_maintenance_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('work_center_id')->nullable();
                $table->unsignedBigInteger('industrial_resource_id')->nullable();
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->string('reference')->nullable();
                $table->string('type')->default('preventive');
                $table->string('status')->default('open');
                $table->date('planned_date')->nullable();
                $table->date('completed_date')->nullable();
                $table->integer('downtime_minutes')->default(0);
                $table->decimal('cost', 16, 2)->default(0);
                $table->text('checklist')->nullable();
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'status'], 'industrial_maintenance_orders_creator_status_idx');
            });
        }

        if (!Schema::hasTable('industrial_cost_records')) {
            Schema::create('industrial_cost_records', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('production_order_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('cost_type')->default('material');
                $table->decimal('amount', 16, 2)->default(0);
                $table->decimal('quantity_basis', 16, 4)->default(0);
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'cost_type'], 'industrial_cost_records_creator_type_idx');
            });
        }

        if (Schema::hasTable('production_work_centers')) {
            Schema::table('production_work_centers', function (Blueprint $table) {
                if (!Schema::hasColumn('production_work_centers', 'industrial_resource_id')) {
                    $table->unsignedBigInteger('industrial_resource_id')->nullable()->after('type');
                }
                if (!Schema::hasColumn('production_work_centers', 'machine_code')) {
                    $table->string('machine_code')->nullable()->after('industrial_resource_id');
                }
                if (!Schema::hasColumn('production_work_centers', 'capacity_hours_per_day')) {
                    $table->decimal('capacity_hours_per_day', 16, 2)->default(0)->after('cost_per_hour');
                }
                if (!Schema::hasColumn('production_work_centers', 'capacity_workers')) {
                    $table->integer('capacity_workers')->default(0)->after('capacity_hours_per_day');
                }
                if (!Schema::hasColumn('production_work_centers', 'is_bottleneck')) {
                    $table->boolean('is_bottleneck')->default(false)->after('capacity_workers');
                }
            });
        }

        if (Schema::hasTable('production_orders')) {
            Schema::table('production_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('production_orders', 'production_routing_id')) {
                    $table->unsignedBigInteger('production_routing_id')->nullable()->after('production_bom_version_id');
                }
                if (!Schema::hasColumn('production_orders', 'production_shift_team_id')) {
                    $table->unsignedBigInteger('production_shift_team_id')->nullable()->after('employee_id');
                }
                if (!Schema::hasColumn('production_orders', 'planned_machine_hours')) {
                    $table->decimal('planned_machine_hours', 16, 2)->default(0)->after('quantity_produced');
                }
                if (!Schema::hasColumn('production_orders', 'planned_labor_hours')) {
                    $table->decimal('planned_labor_hours', 16, 2)->default(0)->after('planned_machine_hours');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('production_orders')) {
            Schema::table('production_orders', function (Blueprint $table) {
                foreach (['production_routing_id', 'production_shift_team_id', 'planned_machine_hours', 'planned_labor_hours'] as $column) {
                    if (Schema::hasColumn('production_orders', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('production_work_centers')) {
            Schema::table('production_work_centers', function (Blueprint $table) {
                foreach (['industrial_resource_id', 'machine_code', 'capacity_hours_per_day', 'capacity_workers', 'is_bottleneck'] as $column) {
                    if (Schema::hasColumn('production_work_centers', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('industrial_cost_records');
        Schema::dropIfExists('industrial_maintenance_orders');
        Schema::dropIfExists('industrial_quality_plans');
        Schema::dropIfExists('industrial_subcontract_orders');
        Schema::dropIfExists('production_shift_teams');
        Schema::dropIfExists('production_routing_steps');
        Schema::dropIfExists('production_routings');
        Schema::dropIfExists('industrial_resources');
    }
};
