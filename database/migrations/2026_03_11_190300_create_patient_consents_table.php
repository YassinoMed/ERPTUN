<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientConsentsTable extends Migration
{
    public function up()
    {
        Schema::create('patient_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('title');
            $table->string('status')->default('signed');
            $table->date('consented_at');
            $table->date('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['created_by', 'patient_id']);
            $table->index(['created_by', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_consents');
    }
}
