<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalAdmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('hospital_admissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('attending_doctor_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->unsignedBigInteger('bed_id')->nullable();
            $table->dateTime('admission_date');
            $table->dateTime('discharge_date')->nullable();
            $table->string('status')->default('admitted');
            $table->string('reason')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('care_plan')->nullable();
            $table->text('discharge_summary')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['created_by', 'patient_id']);
            $table->index(['created_by', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_admissions');
    }
}
