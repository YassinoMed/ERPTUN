<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('name');
            $table->string('event_name')->index();
            $table->text('description')->nullable();
            $table->json('conditions')->nullable();
            $table->json('actions')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('automation_rule_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('event_name')->index();
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('payload')->nullable();
            $table->string('status')->default('pending')->index();
            $table->json('result')->nullable();
            $table->timestamp('triggered_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('document_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_repository_id')->index();
            $table->string('linkable_type');
            $table->unsignedBigInteger('linkable_id');
            $table->string('relation_type')->default('attachment');
            $table->unsignedBigInteger('linked_by')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
            $table->index(['linkable_type', 'linkable_id'], 'document_links_polymorphic_idx');
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_repository_id')->index();
            $table->string('version_label');
            $table->string('file_name')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
        });

        Schema::create('import_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('module')->index();
            $table->string('file_name');
            $table->json('mapping')->nullable();
            $table->json('preview_data')->nullable();
            $table->string('status')->default('draft')->index();
            $table->json('summary')->nullable();
            $table->json('rollback_payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('export_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('module')->index();
            $table->string('format')->default('csv');
            $table->json('filters')->nullable();
            $table->string('status')->default('queued')->index();
            $table->string('file_path')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('saved_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('report_type')->index();
            $table->json('filters')->nullable();
            $table->json('columns')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });

        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('name');
            $table->string('client_key')->unique();
            $table->string('client_secret');
            $table->json('abilities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_client_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('route')->index();
            $table->string('method', 16);
            $table->unsignedSmallInteger('status_code')->nullable()->index();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('ip_address', 64)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->timestamp('requested_at')->useCurrent()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('api_clients');
        Schema::dropIfExists('saved_reports');
        Schema::dropIfExists('export_jobs');
        Schema::dropIfExists('import_jobs');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('document_links');
        Schema::dropIfExists('automation_logs');
        Schema::dropIfExists('automation_rules');
    }
};
