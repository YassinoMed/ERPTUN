<?php

namespace App\Http\Controllers;

use App\Models\LabOrder;
use App\Models\MedicalAppointment;
use App\Models\MedicalInvoice;
use App\Models\NursingCare;
use App\Models\Patient;
use App\Models\PatientLabResult;
use App\Models\PatientPortalMessage;
use App\Models\SurgicalProcedure;
use App\Models\TelemedicineSession;
use App\Models\EmergencyVisit;
use App\Models\HospitalAdmission;
use App\Services\Core\AuditTrailService;
use App\Services\Core\SecurityAccessService;
use Illuminate\Http\Request;

class PatientPortalController extends Controller
{
    public function __construct(
        private readonly SecurityAccessService $securityAccess,
        private readonly AuditTrailService $auditTrail
    ) {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index(Request $request)
    {
        if (!\Auth::user()->can('manage medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $patients = Patient::where('created_by', $creatorId)->orderBy('last_name')->orderBy('first_name')->get();
        $selectedPatient = $patients->first();

        if ($request->filled('patient_id')) {
            $selectedPatient = Patient::where('created_by', $creatorId)->findOrFail($request->integer('patient_id'));
        }

        $appointments = collect();
        $labOrders = collect();
        $labResults = collect();
        $telemedicineSessions = collect();
        $medicalInvoices = collect();
        $surgicalProcedures = collect();
        $portalMessages = collect();
        $emergencyVisits = collect();
        $nursingCares = collect();
        $admissions = collect();

        if ($selectedPatient) {
            $this->auditTrail->record('patient_portal_viewed', [
                'auditable' => $selectedPatient,
                'new_values' => [
                    'portal' => 'patient',
                    'patient_id' => $selectedPatient->id,
                ],
            ]);

            $this->securityAccess->logSensitiveAccess('view_patient_portal', Patient::class, $selectedPatient->id, [
                'patient_name' => trim($selectedPatient->first_name . ' ' . $selectedPatient->last_name),
            ]);

            $appointments = MedicalAppointment::where('created_by', $creatorId)
                ->where('patient_id', $selectedPatient->id)
                ->latest('start_at')
                ->limit(20)
                ->get();

            $labOrders = LabOrder::where('created_by', $creatorId)
                ->where('patient_id', $selectedPatient->id)
                ->latest('ordered_at')
                ->limit(20)
                ->get();

            $labResults = PatientLabResult::where('created_by', $creatorId)
                ->where('patient_id', $selectedPatient->id)
                ->latest('result_date')
                ->limit(20)
                ->get();

            $telemedicineSessions = TelemedicineSession::where('created_by', $creatorId)
                ->where('patient_id', $selectedPatient->id)
                ->latest('scheduled_at')
                ->limit(20)
                ->get();

            $medicalInvoices = MedicalInvoice::with('payments')
                ->where('created_by', $creatorId)
                ->where('patient_id', $selectedPatient->id)
                ->latest('invoice_date')
                ->limit(20)
                ->get();

            $surgicalProcedures = SurgicalProcedure::where('created_by', $creatorId)
                ->where('patient_id', $selectedPatient->id)
                ->latest('scheduled_at')
                ->limit(20)
                ->get();

            $portalMessages = PatientPortalMessage::where('created_by', $creatorId)
                ->where('patient_id', $selectedPatient->id)
                ->latest('sent_at')
                ->limit(30)
                ->get();

            $emergencyVisits = EmergencyVisit::where('created_by', $creatorId)
                ->where('patient_id', $selectedPatient->id)
                ->latest('arrived_at')
                ->limit(10)
                ->get();

            $nursingCares = NursingCare::where('created_by', $creatorId)
                ->where('patient_id', $selectedPatient->id)
                ->latest('scheduled_at')
                ->limit(20)
                ->get();

            $admissions = HospitalAdmission::where('created_by', $creatorId)
                ->where('patient_id', $selectedPatient->id)
                ->latest('id')
                ->limit(10)
                ->get();
        }

        return view('medical.patient_portal', compact(
            'patients',
            'selectedPatient',
            'appointments',
            'labOrders',
            'labResults',
            'telemedicineSessions',
            'medicalInvoices',
            'surgicalProcedures',
            'portalMessages',
            'emergencyVisits',
            'nursingCares',
            'admissions'
        ));
    }

    public function storeMessage(Request $request, Patient $patient)
    {
        if (!\Auth::user()->can('manage medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        if ((int) $patient->created_by !== (int) $creatorId) {
            abort(404);
        }

        $data = $request->validate([
            'subject' => 'required|string|max:191',
            'message' => 'required|string',
            'direction' => 'nullable|string|in:inbound,outbound',
            'status' => 'nullable|string|in:draft,sent,read,closed',
            'sent_at' => 'nullable|date',
        ]);

        $data['patient_id'] = $patient->id;
        $data['direction'] = $data['direction'] ?? 'outbound';
        $data['status'] = $data['status'] ?? 'sent';
        $data['sent_at'] = $data['sent_at'] ?? now();
        $data['created_by'] = $creatorId;

        $message = PatientPortalMessage::create($data);
        $this->auditTrail->record('patient_portal_message_created', [
            'auditable' => $message,
            'new_values' => $message->only(['patient_id', 'subject', 'direction', 'status']),
        ]);

        $this->securityAccess->logSensitiveAccess('create_patient_portal_message', Patient::class, $patient->id, [
            'patient_name' => trim($patient->first_name . ' ' . $patient->last_name),
        ]);

        return redirect()->route('medical.patient-portal', ['patient_id' => $patient->id])->with('success', __('Portal message sent.'));
    }

    public function updateMessageStatus(Request $request, PatientPortalMessage $message)
    {
        if (!\Auth::user()->can('manage medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        if ((int) $message->created_by !== (int) $creatorId) {
            abort(404);
        }

        $data = $request->validate([
            'status' => 'required|string|in:draft,sent,read,closed',
        ]);

        $oldValues = $message->only(['status']);
        $message->update($data);

        $this->auditTrail->record('patient_portal_message_status_updated', [
            'auditable' => $message,
            'old_values' => $oldValues,
            'new_values' => $message->only(['status']),
        ]);

        return redirect()->route('medical.patient-portal', ['patient_id' => $message->patient_id])
            ->with('success', __('Portal message status updated.'));
    }
}
