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
            if (!Schema::hasColumn('plans', 'btp_site_tracking')) {
                $table->integer('btp_site_tracking')->default(1)->after('hedging');
            }
            if (!Schema::hasColumn('plans', 'btp_subcontractors')) {
                $table->integer('btp_subcontractors')->default(1)->after('btp_site_tracking');
            }
            if (!Schema::hasColumn('plans', 'btp_price_breakdowns')) {
                $table->integer('btp_price_breakdowns')->default(1)->after('btp_subcontractors');
            }
            if (!Schema::hasColumn('plans', 'btp_equipment_control')) {
                $table->integer('btp_equipment_control')->default(1)->after('btp_price_breakdowns');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        Schema::table('plans', function (Blueprint $table) {
            $columns = ['btp_site_tracking', 'btp_subcontractors', 'btp_price_breakdowns', 'btp_equipment_control'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('plans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
