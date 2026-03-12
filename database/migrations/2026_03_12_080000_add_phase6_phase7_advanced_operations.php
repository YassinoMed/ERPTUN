<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('production_shopfloor_events')) {
            Schema::create('production_shopfloor_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('production_order_id')->nullable();
                $table->unsignedBigInteger('production_work_center_id')->nullable();
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->string('event_type', 64)->default('status');
                $table->string('status', 64)->default('open');
                $table->decimal('quantity', 16, 3)->default(0);
                $table->integer('downtime_minutes')->default(0);
                $table->dateTime('happened_at');
                $table->text('notes')->nullable();
                $table->integer('created_by')->default(0);
                $table->timestamps();

                $table->index(['created_by', 'production_work_center_id'], 'production_shopfloor_events_creator_center_idx');
                $table->index(['created_by', 'event_type'], 'production_shopfloor_events_creator_type_idx');
                $table->index(['created_by', 'happened_at'], 'production_shopfloor_events_creator_happened_idx');
            });
        }

        if (Schema::hasTable('agri_lots')) {
            Schema::table('agri_lots', function (Blueprint $table) {
                if (!Schema::hasColumn('agri_lots', 'expiry_date')) {
                    $table->date('expiry_date')->nullable()->after('harvest_date');
                }
                if (!Schema::hasColumn('agri_lots', 'source_reference')) {
                    $table->string('source_reference', 191)->nullable()->after('crop_type');
                }
                if (!Schema::hasColumn('agri_lots', 'parcel_origin')) {
                    $table->string('parcel_origin', 191)->nullable()->after('source_reference');
                }
                if (!Schema::hasColumn('agri_lots', 'quality_status')) {
                    $table->string('quality_status', 32)->default('pending')->after('status');
                }
            });
        }

        if (!Schema::hasTable('agri_transformation_batches')) {
            Schema::create('agri_transformation_batches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('input_lot_id');
                $table->unsignedBigInteger('output_lot_id')->nullable();
                $table->string('batch_number', 100);
                $table->string('process_step', 191);
                $table->string('facility_name', 191)->nullable();
                $table->decimal('input_quantity', 18, 3)->default(0);
                $table->decimal('output_quantity', 18, 3)->default(0);
                $table->decimal('waste_quantity', 18, 3)->default(0);
                $table->dateTime('processed_at');
                $table->string('status', 32)->default('completed');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->index();
                $table->timestamps();

                $table->unique(['batch_number', 'created_by']);
                $table->foreign('input_lot_id')->references('id')->on('agri_lots')->onDelete('cascade');
                $table->foreign('output_lot_id')->references('id')->on('agri_lots')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('agri_compliance_checks')) {
            Schema::create('agri_compliance_checks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('lot_id');
                $table->string('control_type', 191);
                $table->string('result', 32)->default('pass');
                $table->string('certificate_ref', 100)->nullable();
                $table->string('measured_value', 100)->nullable();
                $table->string('threshold_value', 100)->nullable();
                $table->dateTime('checked_at');
                $table->text('corrective_action')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->index();
                $table->timestamps();

                $table->index(['created_by', 'result'], 'agri_compliance_checks_creator_result_idx');
                $table->foreign('lot_id')->references('id')->on('agri_lots')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('agri_compliance_checks');
        Schema::dropIfExists('agri_transformation_batches');

        if (Schema::hasTable('agri_lots')) {
            Schema::table('agri_lots', function (Blueprint $table) {
                foreach (['expiry_date', 'source_reference', 'parcel_origin', 'quality_status'] as $column) {
                    if (Schema::hasColumn('agri_lots', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('production_shopfloor_events');
    }
};
