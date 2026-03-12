<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePharmacyMedicationsTable extends Migration
{
    public function up()
    {
        Schema::create('pharmacy_medications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_service_id')->nullable();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('dosage_form')->nullable();
            $table->string('strength')->nullable();
            $table->string('lot_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('stock_quantity', 15, 2)->default(0);
            $table->decimal('reorder_level', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['created_by', 'name']);
            $table->index(['created_by', 'expiry_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pharmacy_medications');
    }
}
