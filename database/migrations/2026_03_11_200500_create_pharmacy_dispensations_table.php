<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePharmacyDispensationsTable extends Migration
{
    public function up()
    {
        Schema::create('pharmacy_dispensations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->unsignedBigInteger('prescription_id')->nullable();
            $table->unsignedBigInteger('dispensed_by')->nullable();
            $table->dateTime('dispensed_at');
            $table->string('status')->default('dispensed');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['created_by', 'patient_id']);
            $table->index(['created_by', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pharmacy_dispensations');
    }
}
