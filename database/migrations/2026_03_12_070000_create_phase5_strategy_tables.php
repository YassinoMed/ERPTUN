<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ppm_portfolios')) {
            Schema::create('ppm_portfolios', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('owner_id')->nullable();
                $table->string('status')->default('active');
                $table->string('priority')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->text('description')->nullable();
                $table->integer('created_by')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ppm_initiatives')) {
            Schema::create('ppm_initiatives', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ppm_portfolio_id');
                $table->unsignedBigInteger('project_id')->nullable();
                $table->unsignedBigInteger('sponsor_id')->nullable();
                $table->string('name');
                $table->string('status')->default('planned');
                $table->string('health_status')->default('green');
                $table->decimal('budget', 15, 2)->default(0);
                $table->decimal('target_value', 15, 2)->nullable();
                $table->decimal('achieved_value', 15, 2)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->text('description')->nullable();
                $table->integer('created_by')->index();
                $table->timestamps();

                $table->foreign('ppm_portfolio_id')->references('id')->on('ppm_portfolios')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('okr_objectives')) {
            Schema::create('okr_objectives', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->unsignedBigInteger('owner_id')->nullable();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->string('cycle')->nullable();
                $table->string('status')->default('draft');
                $table->decimal('progress', 5, 2)->default(0);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->text('description')->nullable();
                $table->integer('created_by')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('okr_key_results')) {
            Schema::create('okr_key_results', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('okr_objective_id');
                $table->string('metric_name');
                $table->decimal('start_value', 15, 2)->default(0);
                $table->decimal('target_value', 15, 2);
                $table->decimal('current_value', 15, 2)->default(0);
                $table->string('unit')->nullable();
                $table->string('status')->default('on_track');
                $table->date('due_date')->nullable();
                $table->integer('created_by')->index();
                $table->timestamps();

                $table->foreign('okr_objective_id')->references('id')->on('okr_objectives')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('nps_campaigns')) {
            Schema::create('nps_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('channel')->default('email');
                $table->string('status')->default('draft');
                $table->string('audience_type')->default('customers');
                $table->dateTime('sent_at')->nullable();
                $table->dateTime('closes_at')->nullable();
                $table->text('description')->nullable();
                $table->integer('created_by')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('nps_responses')) {
            Schema::create('nps_responses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('nps_campaign_id');
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->tinyInteger('score');
                $table->string('sentiment');
                $table->text('feedback')->nullable();
                $table->dateTime('responded_at')->nullable();
                $table->integer('created_by')->index();
                $table->timestamps();

                $table->foreign('nps_campaign_id')->references('id')->on('nps_campaigns')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('nps_responses');
        Schema::dropIfExists('nps_campaigns');
        Schema::dropIfExists('okr_key_results');
        Schema::dropIfExists('okr_objectives');
        Schema::dropIfExists('ppm_initiatives');
        Schema::dropIfExists('ppm_portfolios');
    }
};
