<?php

namespace App\Http\Controllers;

use App\Mail\CommonEmailTemplate;
use App\Models\Employee;
use App\Models\EduAttendance;
use App\Models\EduCertificate;
use App\Models\EduCourse;
use App\Models\EduCourseModule;
use App\Models\EduEnrollment;
use App\Models\EduGrade;
use App\Models\EduSession;
use App\Models\EduTrainerHour;
use App\Models\EduTrainerInvoice;
use App\Models\Trainer;
use App\Models\TrainingType;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EducationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate'])->except(['publicCertificate']);
    }

    public function index()
    {
        if (!Auth::user()->can('manage education')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creatorId = Auth::user()->creatorId();

        $trainingTypes = TrainingType::where('created_by', $creatorId)->get();
        $trainers = Trainer::where('created_by', $creatorId)->get();
        $employees = Employee::where('created_by', $creatorId)->get();

        $courses = EduCourse::where('created_by', $creatorId)->latest()->get();
        $modules = EduCourseModule::where('created_by', $creatorId)->orderBy('sort_order')->get();
        $enrollments = EduEnrollment::where('created_by', $creatorId)->latest()->get();
        $sessions = EduSession::where('created_by', $creatorId)->latest()->get();
        $attendances = EduAttendance::where('created_by', $creatorId)->latest()->get();
        $grades = EduGrade::where('created_by', $creatorId)->latest()->get();
        $certificates = EduCertificate::where('created_by', $creatorId)->latest()->get();
        $trainerHours = EduTrainerHour::where('created_by', $creatorId)->latest()->get();
        $trainerInvoices = EduTrainerInvoice::where('created_by', $creatorId)->latest()->get();

        return view('education.index', compact(
            'trainingTypes',
            'trainers',
            'employees',
            'courses',
            'modules',
            'enrollments',
            'sessions',
            'attendances',
            'grades',
            'certificates',
            'trainerHours',
            'trainerInvoices'
        ));
    }

    public function storeCourse(Request $request)
    {
        if (!Auth::user()->can('manage education')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'delivery_mode' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        EduCourse::create([
            'code' => $request->code,
            'name' => $request->name,
            'training_type_id' => $request->training_type_id,
            'trainer_id' => $request->trainer_id,
            'delivery_mode' => $request->delivery_mode,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('education.index')->with('success', __('Course successfully created.'));
    }

    public function storeModule(Request $request)
    {
        if (!Auth::user()->can('manage education')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'course_id' => 'required|integer',
                'title' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        EduCourseModule::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'content_url' => $request->content_url,
            'duration_minutes' => $request->duration_minutes ?? 0,
            'sort_order' => $request->sort_order ?? 0,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('education.index')->with('success', __('Module successfully created.'));
    }

    public function storeEnrollment(Request $request)
    {
        if (!Auth::user()->can('manage education')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'course_id' => 'required|integer',
                'employee_id' => 'required|integer',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        EduEnrollment::create([
            'course_id' => $request->course_id,
            'employee_id' => $request->employee_id,
            'status' => $request->status ?? 'enrolled',
            'enrolled_at' => $request->enrolled_at ?? now(),
            'completed_at' => $request->completed_at,
            'progress_percent' => $request->progress_percent ?? 0,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('education.index')->with('success', __('Enrollment successfully created.'));
    }

    public function storeSession(Request $request)
    {
        if (!Auth::user()->can('manage education')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'course_id' => 'required|integer',
                'scheduled_at' => 'required|date',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        EduSession::create([
            'course_id' => $request->course_id,
            'scheduled_at' => $request->scheduled_at,
            'duration_hours' => $request->duration_hours ?? 0,
            'location' => $request->location,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('education.index')->with('success', __('Session successfully created.'));
    }

    public function storeAttendance(Request $request)
    {
        if (!Auth::user()->can('manage education')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'session_id' => 'required|integer',
                'employee_id' => 'required|integer',
                'status' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        EduAttendance::create([
            'session_id' => $request->session_id,
            'employee_id' => $request->employee_id,
            'status' => $request->status,
            'note' => $request->note,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('education.index')->with('success', __('Attendance successfully saved.'));
    }

    public function storeGrade(Request $request)
    {
        if (!Auth::user()->can('manage education')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'course_id' => 'required|integer',
                'employee_id' => 'required|integer',
                'score' => 'required|numeric',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        EduGrade::create([
            'course_id' => $request->course_id,
            'employee_id' => $request->employee_id,
            'score' => $request->score,
            'grade' => $request->grade,
            'note' => $request->note,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('education.index')->with('success', __('Grade successfully saved.'));
    }

    public function issueCertificate(Request $request)
    {
        if (!Auth::user()->can('manage education')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'enrollment_id' => 'required|integer',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $creatorId = Auth::user()->creatorId();
        $enrollment = EduEnrollment::where('created_by', $creatorId)->findOrFail($request->enrollment_id);

        $existing = EduCertificate::where('enrollment_id', $enrollment->id)->where('created_by', $creatorId)->first();
        if ($existing) {
            return redirect()->route('education.index')->with('success', __('Certificate already issued.'));
        }

        $hash = hash('sha256', $enrollment->id . '|' . Str::random(40));
        $verificationUrl = route('education.certificates.verify', $hash);
        $certificateNumber = 'EDU-' . strtoupper(Str::random(6)) . '-' . $enrollment->id;

        $certificate = EduCertificate::create([
            'enrollment_id' => $enrollment->id,
            'certificate_number' => $certificateNumber,
            'issued_at' => now(),
            'verification_hash' => $hash,
            'qr_payload' => $verificationUrl,
            'status' => 'issued',
            'sent_to_email' => $request->sent_to_email,
            'created_by' => $creatorId,
        ]);

        if (!empty($request->sent_to_email)) {
            $this->sendCertificateEmail($request->sent_to_email, $certificate);
        }

        return redirect()->route('education.index')->with('success', __('Certificate successfully issued.'));
    }

    public function storeTrainerHour(Request $request)
    {
        if (!Auth::user()->can('manage education')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'trainer_id' => 'required|integer',
                'hours' => 'required|numeric',
                'rate' => 'required|numeric',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $amount = (float) $request->hours * (float) $request->rate;

        EduTrainerHour::create([
            'trainer_id' => $request->trainer_id,
            'course_id' => $request->course_id,
            'session_id' => $request->session_id,
            'hours' => $request->hours,
            'rate' => $request->rate,
            'amount' => $amount,
            'declared_at' => $request->declared_at,
            'note' => $request->note,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('education.index')->with('success', __('Trainer hours successfully saved.'));
    }

    public function storeTrainerInvoice(Request $request)
    {
        if (!Auth::user()->can('manage education')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'trainer_id' => 'required|integer',
                'period_start' => 'required|date',
                'period_end' => 'required|date',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $creatorId = Auth::user()->creatorId();

        $hoursQuery = EduTrainerHour::where('created_by', $creatorId)
            ->where('trainer_id', $request->trainer_id)
            ->whereNull('invoice_id')
            ->whereDate('declared_at', '>=', $request->period_start)
            ->whereDate('declared_at', '<=', $request->period_end);

        $totalHours = (clone $hoursQuery)->sum('hours');
        $totalAmount = (clone $hoursQuery)->sum('amount');

        $invoice = EduTrainerInvoice::create([
            'trainer_id' => $request->trainer_id,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'total_hours' => $totalHours,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'created_by' => $creatorId,
        ]);

        $hoursQuery->update(['invoice_id' => $invoice->id]);

        return redirect()->route('education.index')->with('success', __('Trainer invoice successfully created.'));
    }

    public function publicCertificate(string $hash)
    {
        $certificate = EduCertificate::where('verification_hash', $hash)->firstOrFail();
        $enrollment = $certificate->enrollment;
        $course = $enrollment ? $enrollment->course : null;
        $employee = $enrollment ? $enrollment->employee : null;
        $trainer = $course ? $course->trainer : null;

        return view('education.certificate', compact('certificate', 'course', 'employee', 'trainer'));
    }

    private function sendCertificateEmail(string $email, EduCertificate $certificate): void
    {
        $creatorId = Auth::user()->creatorId();
        $settings = Utility::settingsById($creatorId);

        $data = Utility::getSetting();
        $setting = [
            'mail_driver' => '',
            'mail_host' => '',
            'mail_port' => '',
            'mail_encryption' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_from_address' => '',
            'mail_from_name' => '',
        ];
        foreach ($data as $row) {
            $setting[$row->name] = $row->value;
        }

        config(
            [
                'mail.driver' => $settings['mail_driver'] ? $settings['mail_driver'] : $setting['mail_driver'],
                'mail.host' => $settings['mail_host'] ? $settings['mail_host'] : $setting['mail_host'],
                'mail.port' => $settings['mail_port'] ? $settings['mail_port'] : $setting['mail_port'],
                'mail.encryption' => $settings['mail_encryption'] ? $settings['mail_encryption'] : $setting['mail_encryption'],
                'mail.username' => $settings['mail_username'] ? $settings['mail_username'] : $setting['mail_username'],
                'mail.password' => $settings['mail_password'] ? $settings['mail_password'] : $setting['mail_password'],
                'mail.from.address' => $settings['mail_from_address'] ? $settings['mail_from_address'] : $setting['mail_from_address'],
                'mail.from.name' => $settings['mail_from_name'] ? $settings['mail_from_name'] : $setting['mail_from_name'],
            ]
        );

        $verificationUrl = route('education.certificates.verify', $certificate->verification_hash);
        $content = (object) [
            'from' => $settings['company_name'] ?? config('app.name'),
            'subject' => __('Certificat de formation'),
            'content' => '<p>' . __('Votre certificat de formation est prêt.') . '</p><p><a href="' . $verificationUrl . '">' . __('Voir le certificat') . '</a></p>',
        ];

        try {
            Mail::to($email)->send(new CommonEmailTemplate($content, $settings));
        } catch (\Throwable $e) {
        }
    }
}
