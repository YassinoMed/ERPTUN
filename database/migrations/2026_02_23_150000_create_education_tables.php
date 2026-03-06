<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edu_courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('training_type_id')->nullable();
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->string('delivery_mode')->default('classroom');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('edu_course_modules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->string('content_url')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('edu_enrollments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('status')->default('enrolled');
            $table->date('enrolled_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('edu_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_id');
            $table->dateTime('scheduled_at');
            $table->decimal('duration_hours', 6, 2)->default(0);
            $table->string('location')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('edu_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('status')->default('present');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('edu_grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('employee_id');
            $table->decimal('score', 6, 2)->default(0);
            $table->string('grade')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('edu_certificates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('enrollment_id');
            $table->string('certificate_number');
            $table->dateTime('issued_at');
            $table->string('verification_hash');
            $table->text('qr_payload')->nullable();
            $table->string('status')->default('issued');
            $table->string('sent_to_email')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('edu_trainer_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trainer_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_hours', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('edu_trainer_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trainer_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->decimal('hours', 8, 2)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('declared_at')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edu_trainer_hours');
        Schema::dropIfExists('edu_trainer_invoices');
        Schema::dropIfExists('edu_certificates');
        Schema::dropIfExists('edu_grades');
        Schema::dropIfExists('edu_attendances');
        Schema::dropIfExists('edu_sessions');
        Schema::dropIfExists('edu_enrollments');
        Schema::dropIfExists('edu_course_modules');
        Schema::dropIfExists('edu_courses');
    }
};
