<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btp_site_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->string('file');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('taken_at')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('btp_subcontractors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('btp_subcontract_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subcontractor_id');
            $table->unsignedBigInteger('project_id');
            $table->string('reference')->nullable();
            $table->decimal('amount', 30, 2)->default(0);
            $table->decimal('retention_rate', 8, 2)->default(10);
            $table->decimal('retention_amount', 30, 2)->default(0);
            $table->decimal('vat_rate', 8, 2)->default(19);
            $table->decimal('vat_amount', 30, 2)->default(0);
            $table->decimal('total_due', 30, 2)->default(0);
            $table->string('status')->default('unpaid');
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('btp_subcontract_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('amount', 30, 2)->default(0);
            $table->date('payment_date');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('btp_price_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 30, 2)->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('btp_price_quotes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->string('reference')->nullable();
            $table->decimal('vat_rate', 8, 2)->default(19);
            $table->decimal('subtotal', 30, 2)->default(0);
            $table->decimal('vat_amount', 30, 2)->default(0);
            $table->decimal('total', 30, 2)->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('btp_price_quote_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quote_id');
            $table->unsignedBigInteger('price_item_id')->nullable();
            $table->string('description')->nullable();
            $table->decimal('quantity', 30, 2)->default(0);
            $table->decimal('unit_price', 30, 2)->default(0);
            $table->decimal('line_total', 30, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('btp_equipments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->default('available');
            $table->date('purchase_date')->nullable();
            $table->decimal('rental_rate', 30, 2)->default(0);
            $table->string('fuel_type')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('btp_equipment_usages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('equipment_id');
            $table->unsignedBigInteger('project_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('hours_used', 30, 2)->default(0);
            $table->decimal('fuel_consumed', 30, 2)->default(0);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('btp_equipment_maintenances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('equipment_id');
            $table->string('maintenance_type')->default('preventive');
            $table->date('scheduled_at');
            $table->date('completed_at')->nullable();
            $table->decimal('cost', 30, 2)->default(0);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btp_equipment_maintenances');
        Schema::dropIfExists('btp_equipment_usages');
        Schema::dropIfExists('btp_equipments');
        Schema::dropIfExists('btp_price_quote_items');
        Schema::dropIfExists('btp_price_quotes');
        Schema::dropIfExists('btp_price_items');
        Schema::dropIfExists('btp_subcontract_payments');
        Schema::dropIfExists('btp_subcontract_invoices');
        Schema::dropIfExists('btp_subcontractors');
        Schema::dropIfExists('btp_site_photos');
    }
};
