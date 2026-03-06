<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('cin')->nullable();
            $table->string('cnam_number')->nullable();
            $table->string('gender')->nullable();
            $table->string('blood_group')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('allergies')->nullable();
            $table->string('photo_path')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->index(['created_by', 'cin']);
            $table->index(['created_by', 'cnam_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
