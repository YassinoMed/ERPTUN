<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supports', function (Blueprint $table) {
            $table->boolean('is_internal')->default(false)->after('status');
            $table->string('ticket_type')->nullable()->after('is_internal');
            $table->string('impact_level')->nullable()->after('ticket_type');
            $table->string('urgency_level')->nullable()->after('impact_level');
            $table->unsignedBigInteger('configuration_item_id')->nullable()->after('user');
            $table->dateTime('resolution_due_at')->nullable()->after('end_date');
            $table->dateTime('resolved_at')->nullable()->after('resolution_due_at');
        });
    }

    public function down(): void
    {
        Schema::table('supports', function (Blueprint $table) {
            $table->dropColumn([
                'is_internal',
                'ticket_type',
                'impact_level',
                'urgency_level',
                'configuration_item_id',
                'resolution_due_at',
                'resolved_at',
            ]);
        });
    }
};
