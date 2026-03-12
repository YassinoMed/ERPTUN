<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePharmacyDispensationItemsTable extends Migration
{
    public function up()
    {
        Schema::create('pharmacy_dispensation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pharmacy_dispensation_id');
            $table->unsignedBigInteger('pharmacy_medication_id');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['pharmacy_dispensation_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pharmacy_dispensation_items');
    }
}
