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

        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'traceability')) {
                $table->integer('traceability')->default(1)->after('hotel');
            }
            if (!Schema::hasColumn('plans', 'crop_planning')) {
                $table->integer('crop_planning')->default(1)->after('traceability');
            }
            if (!Schema::hasColumn('plans', 'cooperative')) {
                $table->integer('cooperative')->default(1)->after('crop_planning');
            }
            if (!Schema::hasColumn('plans', 'hedging')) {
                $table->integer('hedging')->default(1)->after('cooperative');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table) {
            $columns = ['traceability', 'crop_planning', 'cooperative', 'hedging'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('plans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
