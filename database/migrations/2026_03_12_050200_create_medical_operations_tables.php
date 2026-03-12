<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('triage_level', 32);
            $table->string('chief_complaint');
            $table->dateTime('arrived_at');
            $table->string('attending_doctor')->nullable();
            $table->enum('status', ['waiting', 'in_care', 'discharged', 'admitted'])->default('waiting');
            $table->text('disposition')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });

        Schema::create('imaging_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->string('modality', 64);
            $table->string('body_part')->nullable();
            $table->string('requested_by')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->enum('status', ['ordered', 'scheduled', 'completed', 'reviewed'])->default('ordered');
            $table->text('report_summary')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });

        Schema::create('nursing_cares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('hospital_admission_id')->nullable();
            $table->string('care_type');
            $table->dateTime('scheduled_at');
            $table->string('nurse_name')->nullable();
            $table->enum('status', ['planned', 'done', 'missed'])->default('planned');
            $table->text('notes')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });

        Schema::create('telemedicine_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('session_link')->nullable();
            $table->dateTime('scheduled_at');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->text('summary')->nullable();
            $table->integer('created_by')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telemedicine_sessions');
        Schema::dropIfExists('nursing_cares');
        Schema::dropIfExists('imaging_orders');
        Schema::dropIfExists('emergency_visits');
    }
};
