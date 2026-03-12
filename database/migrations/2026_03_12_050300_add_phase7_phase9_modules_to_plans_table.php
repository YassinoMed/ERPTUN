<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'agri_operations')) {
                $table->tinyInteger('agri_operations')->default(1)->after('hedging');
            }
            if (!Schema::hasColumn('plans', 'medical_operations')) {
                $table->tinyInteger('medical_operations')->default(1)->after('hospital_admission');
            }
            if (!Schema::hasColumn('plans', 'retail_operations')) {
                $table->tinyInteger('retail_operations')->default(1)->after('delivery_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            foreach (['agri_operations', 'medical_operations', 'retail_operations'] as $column) {
                if (Schema::hasColumn('plans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
