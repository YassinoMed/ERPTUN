<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkflowFieldsToMedicalAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('medical_appointments', function (Blueprint $table) {
            $table->string('appointment_type')->nullable()->after('specialty');
            $table->dateTime('confirmed_at')->nullable()->after('reminder_sent_at');
            $table->dateTime('checked_in_at')->nullable()->after('confirmed_at');
            $table->unsignedInteger('queue_number')->nullable()->after('checked_in_at');
            $table->boolean('is_waiting_list')->default(false)->after('queue_number');
        });
    }

    public function down()
    {
        Schema::table('medical_appointments', function (Blueprint $table) {
            $table->dropColumn([
                'appointment_type',
                'confirmed_at',
                'checked_in_at',
                'queue_number',
                'is_waiting_list',
            ]);
        });
    }
}
