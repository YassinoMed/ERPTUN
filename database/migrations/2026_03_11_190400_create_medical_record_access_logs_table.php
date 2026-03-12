<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalRecordAccessLogsTable extends Migration
{
    public function up()
    {
        Schema::create('medical_record_access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('context')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['created_by', 'patient_id']);
            $table->index(['created_by', 'user_id']);
            $table->index(['created_by', 'action']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_record_access_logs');
    }
}
