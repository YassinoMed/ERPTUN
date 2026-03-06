<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientPrescriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('patient_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consultation_id');
            $table->string('medication_name');
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['created_by', 'consultation_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_prescriptions');
    }
}
