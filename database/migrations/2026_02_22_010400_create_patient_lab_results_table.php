<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientLabResultsTable extends Migration
{
    public function up()
    {
        Schema::create('patient_lab_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->string('test_name');
            $table->string('result_value')->nullable();
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->date('result_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['created_by', 'patient_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_lab_results');
    }
}
