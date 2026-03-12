<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'credit_limit')) {
                $table->decimal('credit_limit', 15, 2)->default(0)->after('credit_balance');
            }
            if (!Schema::hasColumn('customers', 'credit_hold')) {
                $table->boolean('credit_hold')->default(0)->after('credit_limit');
            }
            if (!Schema::hasColumn('customers', 'credit_score')) {
                $table->unsignedInteger('credit_score')->default(0)->after('credit_hold');
            }
            if (!Schema::hasColumn('customers', 'guarantee_notes')) {
                $table->text('guarantee_notes')->nullable()->after('credit_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            foreach (['credit_limit', 'credit_hold', 'credit_score', 'guarantee_notes'] as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
