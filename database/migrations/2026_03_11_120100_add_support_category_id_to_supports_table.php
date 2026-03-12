<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supports', function (Blueprint $table) {
            $table->foreignId('support_category_id')
                ->nullable()
                ->after('user')
                ->constrained('support_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('supports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('support_category_id');
        });
    }
};
