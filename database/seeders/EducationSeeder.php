<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EduAttendance;
use App\Models\EduCertificate;
use App\Models\EduCourse;
use App\Models\EduCourseModule;
use App\Models\EduEnrollment;
use App\Models\EduGrade;
use App\Models\EduSession;
use App\Models\EduTrainerHour;
use App\Models\EduTrainerInvoice;
use App\Models\Employee;
use App\Models\Trainer;
use App\Models\TrainingType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        $company = User::where('type', 'company')->first();
        if (!$company) {
            $company = User::where('type', 'super admin')->first();
        }
        if (!$company) {
            return;
        }

        $createdBy = $company->id;

        $branch = Branch::updateOrCreate(
            ['name' => 'Siège', 'created_by' => $createdBy],
            ['name' => 'Siège', 'created_by' => $createdBy]
        );

        $department = Department::updateOrCreate(
            ['name' => 'Formation', 'created_by' => $createdBy],
            ['name' => 'Formation', 'created_by' => $createdBy]
        );
        $department->branch_id = $branch->id;
        $department->save();

        $designation = Designation::updateOrCreate(
            ['name' => 'Formateur', 'created_by' => $createdBy],
            [
                'name' => 'Formateur',
                'created_by' => $createdBy,
                'branch_id' => $branch->id,
                'department_id' => $department->id,
            ]
        );

        $employee = Employee::updateOrCreate(
            ['email' => 'apprenant@example.com', 'created_by' => $createdBy],
            [
                'name' => 'Apprenant Démo',
                'email' => 'apprenant@example.com',
                'employee_id' => 'EMP-0001',
                'branch_id' => $branch->id,
                'department_id' => $department->id,
                'designation_id' => $designation->id,
                'company_doj' => now()->toDateString(),
                'created_by' => $createdBy,
            ]
        );

        $trainer = Trainer::updateOrCreate(
            ['email' => 'trainer@example.com', 'created_by' => $createdBy],
            [
                'branch' => $branch->id,
                'firstname' => 'Jean',
                'lastname' => 'Dupont',
                'contact' => '0600000000',
                'email' => 'trainer@example.com',
                'address' => 'Paris',
                'expertise' => 'Leadership',
                'created_by' => $createdBy,
            ]
        );

        $trainingType = TrainingType::updateOrCreate(
            ['name' => 'Leadership', 'created_by' => $createdBy],
            ['name' => 'Leadership', 'created_by' => $createdBy]
        );

        $course = EduCourse::updateOrCreate(
            ['code' => 'EDU-LEAD-01', 'created_by' => $createdBy],
            [
                'code' => 'EDU-LEAD-01',
                'name' => 'Leadership efficace',
                'training_type_id' => $trainingType->id,
                'trainer_id' => $trainer->id,
                'delivery_mode' => 'blended',
                'start_date' => now()->addWeek()->toDateString(),
                'end_date' => now()->addWeeks(2)->toDateString(),
                'description' => 'Parcours leadership avec modules en ligne et ateliers.',
                'created_by' => $createdBy,
            ]
        );

        EduCourseModule::updateOrCreate(
            ['course_id' => $course->id, 'title' => 'Fondations du leadership'],
            [
                'course_id' => $course->id,
                'title' => 'Fondations du leadership',
                'content_url' => 'https://example.com/elearning/leadership-1',
                'duration_minutes' => 45,
                'sort_order' => 1,
                'created_by' => $createdBy,
            ]
        );

        EduCourseModule::updateOrCreate(
            ['course_id' => $course->id, 'title' => 'Communication et feedback'],
            [
                'course_id' => $course->id,
                'title' => 'Communication et feedback',
                'content_url' => 'https://example.com/elearning/leadership-2',
                'duration_minutes' => 60,
                'sort_order' => 2,
                'created_by' => $createdBy,
            ]
        );

        $sessionDate = Carbon::now()->addDays(10)->setTime(9, 0, 0);
        $session = EduSession::updateOrCreate(
            ['course_id' => $course->id, 'scheduled_at' => $sessionDate],
            [
                'course_id' => $course->id,
                'scheduled_at' => $sessionDate,
                'duration_hours' => 3,
                'location' => 'Salle A',
                'created_by' => $createdBy,
            ]
        );

        $enrollment = EduEnrollment::updateOrCreate(
            ['course_id' => $course->id, 'employee_id' => $employee->id],
            [
                'course_id' => $course->id,
                'employee_id' => $employee->id,
                'status' => 'enrolled',
                'enrolled_at' => now()->toDateString(),
                'completed_at' => null,
                'progress_percent' => 25,
                'created_by' => $createdBy,
            ]
        );

        EduAttendance::updateOrCreate(
            ['session_id' => $session->id, 'employee_id' => $employee->id],
            [
                'session_id' => $session->id,
                'employee_id' => $employee->id,
                'status' => 'present',
                'note' => 'Participation active',
                'created_by' => $createdBy,
            ]
        );

        EduGrade::updateOrCreate(
            ['course_id' => $course->id, 'employee_id' => $employee->id],
            [
                'course_id' => $course->id,
                'employee_id' => $employee->id,
                'score' => 82,
                'grade' => 'B',
                'note' => 'Bon niveau général.',
                'created_by' => $createdBy,
            ]
        );

        $verificationHash = hash('sha256', $enrollment->id . '|' . $employee->id . '|' . now()->timestamp);
        EduCertificate::updateOrCreate(
            ['enrollment_id' => $enrollment->id],
            [
                'enrollment_id' => $enrollment->id,
                'certificate_number' => 'CERT-' . Str::upper(Str::random(8)),
                'issued_at' => now(),
                'verification_hash' => $verificationHash,
                'qr_payload' => 'education:certificate:' . $verificationHash,
                'status' => 'issued',
                'sent_to_email' => $employee->email,
                'created_by' => $createdBy,
            ]
        );

        $trainerHour = EduTrainerHour::updateOrCreate(
            ['trainer_id' => $trainer->id, 'session_id' => $session->id],
            [
                'trainer_id' => $trainer->id,
                'course_id' => $course->id,
                'session_id' => $session->id,
                'hours' => 3,
                'rate' => 50,
                'amount' => 150,
                'declared_at' => now()->toDateString(),
                'note' => 'Atelier présentiel',
                'created_by' => $createdBy,
            ]
        );

        $periodStart = $sessionDate->copy()->startOfMonth()->toDateString();
        $periodEnd = $sessionDate->copy()->endOfMonth()->toDateString();
        $invoice = EduTrainerInvoice::updateOrCreate(
            ['trainer_id' => $trainer->id, 'period_start' => $periodStart, 'period_end' => $periodEnd],
            [
                'trainer_id' => $trainer->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_hours' => $trainerHour->hours,
                'total_amount' => $trainerHour->amount,
                'status' => 'sent',
                'created_by' => $createdBy,
            ]
        );

        $trainerHour->invoice_id = $invoice->id;
        $trainerHour->save();
    }
}
