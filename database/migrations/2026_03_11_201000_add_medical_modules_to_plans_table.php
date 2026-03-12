<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMedicalModulesToPlansTable extends Migration
{
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('medical_service')->default(0)->after('document_repository');
            $table->boolean('medical_invoice')->default(0)->after('medical_service');
            $table->boolean('pharmacy_medication')->default(0)->after('medical_invoice');
            $table->boolean('pharmacy_dispensation')->default(0)->after('pharmacy_medication');
            $table->boolean('hospital_room')->default(0)->after('pharmacy_dispensation');
            $table->boolean('hospital_bed')->default(0)->after('hospital_room');
            $table->boolean('hospital_admission')->default(0)->after('hospital_bed');
        });
    }

    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'medical_service',
                'medical_invoice',
                'pharmacy_medication',
                'pharmacy_dispensation',
                'hospital_room',
                'hospital_bed',
                'hospital_admission',
            ]);
        });
    }
}
