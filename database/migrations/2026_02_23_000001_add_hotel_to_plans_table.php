<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        if (!Schema::hasColumn('plans', 'hotel')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->integer('hotel')->default(1)->after('saas');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        if (Schema::hasColumn('plans', 'hotel')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->dropColumn('hotel');
            });
        }
    }
};
