<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agri_weighings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lot_id')->nullable();
            $table->unsignedBigInteger('cooperative_id')->nullable();
            $table->string('producer_name')->nullable();
            $table->decimal('gross_weight', 15, 3)->default(0);
            $table->decimal('tare_weight', 15, 3)->default(0);
            $table->decimal('net_weight', 15, 3)->default(0);
            $table->decimal('moisture_percent', 8, 2)->default(0);
            $table->string('quality_grade', 64)->nullable();
            $table->date('weighing_date');
            $table->text('notes')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });

        Schema::create('agri_cold_storage_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lot_id')->nullable();
            $table->string('facility_name');
            $table->string('chamber_name')->nullable();
            $table->decimal('temperature', 8, 2)->nullable();
            $table->decimal('humidity', 8, 2)->nullable();
            $table->decimal('quantity', 15, 3)->default(0);
            $table->date('entry_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['stored', 'released', 'blocked'])->default('stored');
            $table->text('notes')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });

        Schema::create('agri_export_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lot_id')->nullable();
            $table->string('shipment_ref');
            $table->string('customer_name');
            $table->string('destination_country');
            $table->string('container_no')->nullable();
            $table->string('incoterm', 32)->nullable();
            $table->decimal('shipped_quantity', 15, 3)->default(0);
            $table->date('departure_date');
            $table->enum('status', ['draft', 'ready', 'shipped', 'delivered'])->default('draft');
            $table->string('document_ref')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
            $table->unique(['shipment_ref', 'created_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agri_export_shipments');
        Schema::dropIfExists('agri_cold_storage_records');
        Schema::dropIfExists('agri_weighings');
    }
};
