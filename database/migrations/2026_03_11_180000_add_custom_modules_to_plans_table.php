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
            if (!Schema::hasColumn('plans', 'board_meeting')) {
                $table->integer('board_meeting')->default(1)->after('btp_equipment_control');
            }
            if (!Schema::hasColumn('plans', 'cap_table')) {
                $table->integer('cap_table')->default(1)->after('board_meeting');
            }
            if (!Schema::hasColumn('plans', 'subsidiary')) {
                $table->integer('subsidiary')->default(1)->after('cap_table');
            }
            if (!Schema::hasColumn('plans', 'customer_recovery')) {
                $table->integer('customer_recovery')->default(1)->after('subsidiary');
            }
            if (!Schema::hasColumn('plans', 'visitor')) {
                $table->integer('visitor')->default(1)->after('customer_recovery');
            }
            if (!Schema::hasColumn('plans', 'innovation_idea')) {
                $table->integer('innovation_idea')->default(1)->after('visitor');
            }
            if (!Schema::hasColumn('plans', 'knowledge_base')) {
                $table->integer('knowledge_base')->default(1)->after('innovation_idea');
            }
            if (!Schema::hasColumn('plans', 'document_repository')) {
                $table->integer('document_repository')->default(1)->after('knowledge_base');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table) {
            $columns = [
                'board_meeting',
                'cap_table',
                'subsidiary',
                'customer_recovery',
                'visitor',
                'innovation_idea',
                'knowledge_base',
                'document_repository',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('plans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
