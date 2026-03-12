<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archived_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('record_owner_id')->nullable()->index();
            $table->string('record_type');
            $table->unsignedBigInteger('record_id');
            $table->string('display_name')->nullable();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('archived_by')->nullable()->index();
            $table->timestamp('archived_at')->nullable()->index();
            $table->unsignedBigInteger('restored_by')->nullable()->index();
            $table->timestamp('restored_at')->nullable()->index();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['record_type', 'record_id']);
        });

        foreach (['customers', 'venders', 'product_services', 'patients'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->timestamp('archived_at')->nullable()->after('created_by')->index();
                $table->unsignedBigInteger('archived_by')->nullable()->after('archived_at')->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['customers', 'venders', 'product_services', 'patients'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['archived_at', 'archived_by']);
            });
        }

        Schema::dropIfExists('archived_records');
    }
};
