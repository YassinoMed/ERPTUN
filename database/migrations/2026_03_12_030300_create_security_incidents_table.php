<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('incident_reference')->nullable();
            $table->string('incident_type')->nullable();
            $table->string('severity')->default('medium');
            $table->string('status')->default('open');
            $table->string('affected_module')->nullable();
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->dateTime('detected_at')->nullable();
            $table->text('summary')->nullable();
            $table->text('containment_actions')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_incidents');
    }
};
