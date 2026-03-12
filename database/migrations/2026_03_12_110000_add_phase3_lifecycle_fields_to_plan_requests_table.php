<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('plan_requests', 'current_plan_id')) {
                $table->unsignedBigInteger('current_plan_id')->nullable()->after('plan_id');
            }
            if (! Schema::hasColumn('plan_requests', 'status')) {
                $table->string('status', 30)->default('pending')->after('duration');
            }
            if (! Schema::hasColumn('plan_requests', 'request_note')) {
                $table->text('request_note')->nullable()->after('status');
            }
            if (! Schema::hasColumn('plan_requests', 'review_note')) {
                $table->text('review_note')->nullable()->after('request_note');
            }
            if (! Schema::hasColumn('plan_requests', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable()->after('review_note');
            }
            if (! Schema::hasColumn('plan_requests', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plan_requests', function (Blueprint $table) {
            $columns = [
                'current_plan_id',
                'status',
                'request_note',
                'review_note',
                'reviewed_by',
                'reviewed_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('plan_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
