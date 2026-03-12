<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalBedsTable extends Migration
{
    public function up()
    {
        Schema::create('hospital_beds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_room_id');
            $table->string('bed_number');
            $table->string('status')->default('available');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['created_by', 'hospital_room_id']);
            $table->index(['created_by', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_beds');
    }
}
