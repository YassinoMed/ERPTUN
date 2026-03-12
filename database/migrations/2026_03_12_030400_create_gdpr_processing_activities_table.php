<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdpr_processing_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity_name');
            $table->string('activity_code')->nullable();
            $table->string('data_category')->nullable();
            $table->string('purpose')->nullable();
            $table->string('lawful_basis')->nullable();
            $table->string('processor_name')->nullable();
            $table->string('retention_period')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_processing_activities');
    }
};
