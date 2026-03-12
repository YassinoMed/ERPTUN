<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardMeetingEmployeesTable extends Migration
{
    public function up()
    {
        Schema::create('board_meeting_employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('board_meeting_id');
            $table->integer('employee_id');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('board_meeting_employees');
    }
}
