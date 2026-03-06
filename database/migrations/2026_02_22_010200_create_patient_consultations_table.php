<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientConsultationsTable extends Migration
{
    public function up()
    {
        Schema::create('patient_consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->dateTime('consultation_date');
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['created_by', 'patient_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_consultations');
    }
}
