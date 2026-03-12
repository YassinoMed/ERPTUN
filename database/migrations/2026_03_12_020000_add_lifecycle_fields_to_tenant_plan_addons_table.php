<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenant_plan_addons', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->default(0)->after('status');
            $table->string('billing_cycle')->nullable()->after('amount');
            $table->timestamp('renews_at')->nullable()->after('expires_at');
            $table->timestamp('cancelled_at')->nullable()->after('renews_at');
            $table->text('cancel_reason')->nullable()->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenant_plan_addons', function (Blueprint $table) {
            $table->dropColumn(['amount', 'billing_cycle', 'renews_at', 'cancelled_at', 'cancel_reason']);
        });
    }
};
