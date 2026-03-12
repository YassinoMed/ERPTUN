<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationPreferencesTable extends Migration
{
    public function up()
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('notification_type');
            $table->boolean('in_app')->default(true);
            $table->boolean('email')->default(false);
            $table->boolean('sms')->default(false);
            $table->boolean('whatsapp')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->unique(['user_id', 'notification_type']);
            $table->index(['created_by']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_preferences');
    }
}
