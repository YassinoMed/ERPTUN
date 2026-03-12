<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('export_jobs', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('scheduled_for');
            $table->unsignedInteger('attempts')->default(0)->after('started_at');
            $table->text('error_message')->nullable()->after('attempts');
        });
    }

    public function down(): void
    {
        Schema::table('export_jobs', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'attempts', 'error_message']);
        });
    }
};
