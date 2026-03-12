<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardMeetingsTable extends Migration
{
    public function up()
    {
        Schema::create('board_meetings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('branch_id')->default(0);
            $table->string('title');
            $table->date('meeting_date');
            $table->time('meeting_time');
            $table->string('status')->default('scheduled');
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->text('agenda')->nullable();
            $table->text('minutes')->nullable();
            $table->text('resolution_summary')->nullable();
            $table->text('external_guests')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('board_meetings');
    }
}
