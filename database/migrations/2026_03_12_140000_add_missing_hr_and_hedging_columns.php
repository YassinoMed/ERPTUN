<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                if (! Schema::hasColumn('departments', 'code')) {
                    $table->string('code', 50)->nullable()->after('name');
                }
                if (! Schema::hasColumn('departments', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('code');
                }
            });
        }

        if (Schema::hasTable('contract_types')) {
            Schema::table('contract_types', function (Blueprint $table) {
                if (! Schema::hasColumn('contract_types', 'slug')) {
                    $table->string('slug', 100)->nullable()->after('name');
                }
                if (! Schema::hasColumn('contract_types', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                if (Schema::hasColumn('departments', 'is_active')) {
                    $table->dropColumn('is_active');
                }
                if (Schema::hasColumn('departments', 'code')) {
                    $table->dropColumn('code');
                }
            });
        }

        if (Schema::hasTable('contract_types')) {
            Schema::table('contract_types', function (Blueprint $table) {
                if (Schema::hasColumn('contract_types', 'description')) {
                    $table->dropColumn('description');
                }
                if (Schema::hasColumn('contract_types', 'slug')) {
                    $table->dropColumn('slug');
                }
            });
        }
    }
};
