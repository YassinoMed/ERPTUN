<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_onboarding_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->unique();
            $table->json('checklist')->nullable();
            $table->json('completed_steps')->nullable();
            $table->unsignedBigInteger('configured_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('plan_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id')->nullable()->index();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->string('billing_cycle')->default('monthly');
            $table->json('limits')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tenant_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('metric_key')->index();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->decimal('usage_value', 15, 2)->default(0);
            $table->decimal('limit_value', 15, 2)->nullable();
            $table->timestamp('resets_at')->nullable();
            $table->timestamps();
            $table->unique(['created_by', 'metric_key', 'subject_type', 'subject_id'], 'tenant_usages_unique_metric');
        });

        Schema::create('saved_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('module')->index();
            $table->string('name');
            $table->json('filters')->nullable();
            $table->json('columns')->nullable();
            $table->json('sorts')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('timeline_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('timelineable_type')->nullable();
            $table->unsignedBigInteger('timelineable_id')->nullable();
            $table->string('entry_type')->default('system')->index();
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('happened_at')->useCurrent()->index();
            $table->timestamps();
            $table->index(['timelineable_type', 'timelineable_id'], 'timeline_entries_polymorphic_idx');
        });

        Schema::create('internal_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('notable_type');
            $table->unsignedBigInteger('notable_id');
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->string('visibility')->default('private');
            $table->timestamps();
            $table->index(['notable_type', 'notable_id'], 'internal_notes_polymorphic_idx');
        });

        Schema::create('data_quality_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('issue_type')->index();
            $table->string('module')->index();
            $table->string('record_type')->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('duplicate_type')->nullable();
            $table->unsignedBigInteger('duplicate_id')->nullable();
            $table->string('status')->default('open')->index();
            $table->json('payload')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_session_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('session_id')->index();
            $table->string('ip_address', 64)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->timestamp('login_at')->useCurrent()->index();
            $table->timestamp('logout_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('sensitive_access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('resource_type')->index();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('action')->index();
            $table->string('route')->nullable();
            $table->string('ip_address', 64)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensitive_access_logs');
        Schema::dropIfExists('user_session_logs');
        Schema::dropIfExists('data_quality_issues');
        Schema::dropIfExists('internal_notes');
        Schema::dropIfExists('timeline_entries');
        Schema::dropIfExists('saved_views');
        Schema::dropIfExists('tenant_usages');
        Schema::dropIfExists('plan_addons');
        Schema::dropIfExists('tenant_onboarding_checklists');
    }
};
