<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['created_by', 'created_at'], 'audit_logs_owner_created_at_idx');
            $table->index(['auditable_type', 'auditable_id'], 'audit_logs_auditable_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read'], 'notifications_user_read_idx');
            $table->index(['user_id', 'created_at'], 'notifications_user_created_idx');
        });

        Schema::table('document_repositories', function (Blueprint $table) {
            $table->index(['created_by', 'status'], 'document_repositories_owner_status_idx');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index(['created_by', 'email'], 'customers_owner_email_idx');
            $table->index(['created_by', 'name'], 'customers_owner_name_idx');
        });

        Schema::table('venders', function (Blueprint $table) {
            $table->index(['created_by', 'email'], 'venders_owner_email_idx');
            $table->index(['created_by', 'name'], 'venders_owner_name_idx');
        });
    }

    public function down(): void
    {
        Schema::table('venders', function (Blueprint $table) {
            $table->dropIndex('venders_owner_email_idx');
            $table->dropIndex('venders_owner_name_idx');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_owner_email_idx');
            $table->dropIndex('customers_owner_name_idx');
        });

        Schema::table('document_repositories', function (Blueprint $table) {
            $table->dropIndex('document_repositories_owner_status_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_read_idx');
            $table->dropIndex('notifications_user_created_idx');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('audit_logs_owner_created_at_idx');
            $table->dropIndex('audit_logs_auditable_idx');
        });
    }
};
