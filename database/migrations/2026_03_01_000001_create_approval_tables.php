<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('resource_type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
        });

        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_flow_id');
            $table->string('name');
            $table->unsignedInteger('sequence')->default(1);
            $table->json('rule')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->foreign('approval_flow_id')->references('id')->on('approval_flows')->onDelete('cascade');
        });

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_flow_id')->nullable();
            $table->string('resource_type')->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->foreign('approval_flow_id')->references('id')->on('approval_flows')->onDelete('set null');
        });

        Schema::create('approval_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_request_id');
            $table->unsignedBigInteger('approval_step_id')->nullable();
            $table->string('action')->default('approved');
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('acted_by')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->foreign('approval_request_id')->references('id')->on('approval_requests')->onDelete('cascade');
            $table->foreign('approval_step_id')->references('id')->on('approval_steps')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_actions');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_steps');
        Schema::dropIfExists('approval_flows');
    }
};
