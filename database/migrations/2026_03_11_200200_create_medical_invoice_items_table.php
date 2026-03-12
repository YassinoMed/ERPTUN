<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalInvoiceItemsTable extends Migration
{
    public function up()
    {
        Schema::create('medical_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_invoice_id');
            $table->unsignedBigInteger('medical_service_id')->nullable();
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('coverage_rate', 8, 2)->default(0);
            $table->decimal('covered_amount', 15, 2)->default(0);
            $table->decimal('patient_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['medical_invoice_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_invoice_items');
    }
}
