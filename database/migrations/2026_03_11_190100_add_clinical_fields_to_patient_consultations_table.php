<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClinicalFieldsToPatientConsultationsTable extends Migration
{
    public function up()
    {
        Schema::table('patient_consultations', function (Blueprint $table) {
            $table->string('reason_for_visit')->nullable()->after('title');
            $table->decimal('temperature', 5, 2)->nullable()->after('reason_for_visit');
            $table->unsignedInteger('heart_rate')->nullable()->after('temperature');
            $table->string('blood_pressure')->nullable()->after('heart_rate');
            $table->unsignedInteger('respiratory_rate')->nullable()->after('blood_pressure');
            $table->decimal('weight', 8, 2)->nullable()->after('respiratory_rate');
            $table->decimal('height', 8, 2)->nullable()->after('weight');
            $table->text('clinical_observations')->nullable()->after('diagnosis');
            $table->text('requested_exams')->nullable()->after('clinical_observations');
            $table->text('medical_certificate')->nullable()->after('requested_exams');
            $table->date('sick_leave_start')->nullable()->after('medical_certificate');
            $table->date('sick_leave_end')->nullable()->after('sick_leave_start');
        });
    }

    public function down()
    {
        Schema::table('patient_consultations', function (Blueprint $table) {
            $table->dropColumn([
                'reason_for_visit',
                'temperature',
                'heart_rate',
                'blood_pressure',
                'respiratory_rate',
                'weight',
                'height',
                'clinical_observations',
                'requested_exams',
                'medical_certificate',
                'sick_leave_start',
                'sick_leave_end',
            ]);
        });
    }
}
