<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('hospital_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('department')->nullable();
            $table->string('room_type')->nullable();
            $table->string('status')->default('available');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index(['created_by', 'department']);
            $table->index(['created_by', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('hospital_rooms');
    }
}
