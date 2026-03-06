<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::create('medical_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->string('room')->nullable();
            $table->string('specialty')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('status')->default('scheduled');
            $table->string('reminder_channel')->nullable();
            $table->dateTime('reminder_at')->nullable();
            $table->dateTime('reminder_sent_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['created_by', 'doctor_id', 'start_at']);
            $table->index(['created_by', 'patient_id', 'start_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_appointments');
    }
}
