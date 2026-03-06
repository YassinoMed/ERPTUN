<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToPatientConsultationsTable extends Migration
{
    public function up()
    {
        Schema::table('patient_consultations', function (Blueprint $table) {
            $table->string('doctor_name')->nullable()->after('consultation_date');
            $table->string('title')->nullable()->after('doctor_name');
            $table->date('next_visit_date')->nullable()->after('title');
        });
    }

    public function down()
    {
        Schema::table('patient_consultations', function (Blueprint $table) {
            $table->dropColumn(['doctor_name', 'title', 'next_visit_date']);
        });
    }
}
