<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advanced_module_feature_states', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('module_key');
            $table->string('feature_key');
            $table->string('status')->default('planned');
            $table->string('priority')->default('medium');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['owner_id', 'module_key', 'feature_key'], 'advanced_module_feature_states_unique');
        });

        Schema::create('module_kpi_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('module_key')->index();
            $table->json('kpis');
            $table->timestamp('calculated_at')->index();
            $table->timestamps();
        });

        Schema::create('module_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('module_key')->index();
            $table->string('alert_key');
            $table->string('severity')->default('info');
            $table->string('status')->default('open')->index();
            $table->string('title');
            $table->text('message');
            $table->json('payload')->nullable();
            $table->timestamp('detected_at')->nullable()->index();
            $table->timestamp('resolved_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['owner_id', 'module_key', 'alert_key', 'status'], 'module_alerts_owner_module_key_status_unique');
        });

        Schema::create('module_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->index();
            $table->string('module_key')->index();
            $table->string('recommendation_key');
            $table->string('priority')->default('medium');
            $table->string('status')->default('pending')->index();
            $table->string('title');
            $table->text('description');
            $table->json('payload')->nullable();
            $table->timestamp('generated_at')->nullable()->index();
            $table->timestamp('applied_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['owner_id', 'module_key', 'recommendation_key', 'status'], 'module_recommendations_owner_module_key_status_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_recommendations');
        Schema::dropIfExists('module_alerts');
        Schema::dropIfExists('module_kpi_snapshots');
        Schema::dropIfExists('advanced_module_feature_states');
    }
};
