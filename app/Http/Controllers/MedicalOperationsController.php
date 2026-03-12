<?php

namespace App\Http\Controllers;

use App\Models\BiomedicalAsset;
use App\Models\EmergencyVisit;
use App\Models\HospitalAdmission;
use App\Models\ImagingOrder;
use App\Models\LabOrder;
use App\Models\MedicalAppointment;
use App\Models\MedicalSpecialty;
use App\Models\PatientLabResult;
use App\Models\NursingCare;
use App\Models\Patient;
use App\Models\PatientConsultation;
use App\Models\SurgicalProcedure;
use App\Models\TelemedicineSession;
use App\Models\MedicalInvoice;
use App\Services\Core\AuditTrailService;
use App\Services\Core\SecurityAccessService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicalOperationsController extends Controller
{
    public function __construct(
        private readonly SecurityAccessService $securityAccess,
        private readonly AuditTrailService $auditTrail
    ) {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $patients = Patient::where('created_by', $creatorId)->latest('id')->get();
        $consultations = PatientConsultation::where('created_by', $creatorId)->latest('consultation_date')->limit(25)->get();
        $appointments = MedicalAppointment::where('created_by', $creatorId)->latest('start_at')->limit(25)->get();
        $admissions = HospitalAdmission::where('created_by', $creatorId)->latest('id')->limit(25)->get();

        $emergencyVisits = EmergencyVisit::with('patient')->where('created_by', $creatorId)->latest('arrived_at')->limit(20)->get();
        $imagingOrders = ImagingOrder::with(['patient', 'consultation'])->where('created_by', $creatorId)->latest('scheduled_at')->limit(20)->get();
        $nursingCares = NursingCare::with(['patient', 'admission'])->where('created_by', $creatorId)->latest('scheduled_at')->limit(20)->get();
        $telemedicineSessions = TelemedicineSession::with(['patient', 'appointment'])->where('created_by', $creatorId)->latest('scheduled_at')->limit(20)->get();
        $labOrders = LabOrder::with(['patient', 'consultation'])->where('created_by', $creatorId)->latest('ordered_at')->limit(20)->get();
        $surgicalProcedures = SurgicalProcedure::with(['patient', 'admission'])->where('created_by', $creatorId)->latest('scheduled_at')->limit(20)->get();
        $biomedicalAssets = BiomedicalAsset::where('created_by', $creatorId)->latest('id')->get();
        $medicalSpecialties = MedicalSpecialty::where('created_by', $creatorId)->latest('id')->get();

        return view('medical.operations', compact(
            'patients',
            'consultations',
            'appointments',
            'admissions',
            'emergencyVisits',
            'imagingOrders',
            'nursingCares',
            'telemedicineSessions',
            'labOrders',
            'surgicalProcedures',
            'biomedicalAssets',
            'medicalSpecialties'
        ));
    }

    public function storeEmergencyVisit(Request $request)
    {
        if (!\Auth::user()->can('create medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'patient_id' => 'required|integer',
            'triage_level' => 'required|string|max:32',
            'chief_complaint' => 'required|string|max:191',
            'arrived_at' => 'required|date',
            'attending_doctor' => 'nullable|string|max:191',
            'status' => 'nullable|string|in:waiting,in_care,discharged,admitted',
            'disposition' => 'nullable|string',
        ]);

        $patient = Patient::where('created_by', $creatorId)->findOrFail($data['patient_id']);
        $data['status'] = $data['status'] ?? 'waiting';
        $data['created_by'] = $creatorId;

        EmergencyVisit::create($data);
        $this->securityAccess->logSensitiveAccess('create_emergency_visit', Patient::class, $patient->id, [
            'patient_name' => trim($patient->first_name . ' ' . $patient->last_name),
        ]);

        return redirect()->route('medical.operations.index')->with('success', __('Emergency visit recorded.'));
    }

    public function storeImagingOrder(Request $request)
    {
        if (!\Auth::user()->can('create medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'patient_id' => 'required|integer',
            'consultation_id' => 'nullable|integer',
            'modality' => 'required|string|max:64',
            'body_part' => 'nullable|string|max:191',
            'requested_by' => 'nullable|string|max:191',
            'scheduled_at' => 'nullable|date',
            'status' => 'nullable|string|in:ordered,scheduled,completed,reviewed',
            'report_summary' => 'nullable|string',
        ]);

        $patient = Patient::where('created_by', $creatorId)->findOrFail($data['patient_id']);
        if (!empty($data['consultation_id'])) {
            PatientConsultation::where('created_by', $creatorId)->findOrFail($data['consultation_id']);
        }

        $data['status'] = $data['status'] ?? 'ordered';
        $data['created_by'] = $creatorId;
        ImagingOrder::create($data);

        $this->securityAccess->logSensitiveAccess('create_imaging_order', Patient::class, $patient->id, [
            'patient_name' => trim($patient->first_name . ' ' . $patient->last_name),
        ]);

        return redirect()->route('medical.operations.index')->with('success', __('Imaging order saved.'));
    }

    public function storeNursingCare(Request $request)
    {
        if (!\Auth::user()->can('create medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'patient_id' => 'required|integer',
            'hospital_admission_id' => 'nullable|integer',
            'care_type' => 'required|string|max:191',
            'scheduled_at' => 'required|date',
            'nurse_name' => 'nullable|string|max:191',
            'status' => 'nullable|string|in:planned,done,missed',
            'notes' => 'nullable|string',
        ]);

        $patient = Patient::where('created_by', $creatorId)->findOrFail($data['patient_id']);
        if (!empty($data['hospital_admission_id'])) {
            HospitalAdmission::where('created_by', $creatorId)->findOrFail($data['hospital_admission_id']);
        }

        $data['status'] = $data['status'] ?? 'planned';
        $data['created_by'] = $creatorId;
        NursingCare::create($data);

        $this->securityAccess->logSensitiveAccess('create_nursing_care', Patient::class, $patient->id, [
            'patient_name' => trim($patient->first_name . ' ' . $patient->last_name),
        ]);

        return redirect()->route('medical.operations.index')->with('success', __('Nursing care recorded.'));
    }

    public function storeTelemedicineSession(Request $request)
    {
        if (!\Auth::user()->can('create medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'patient_id' => 'required|integer',
            'appointment_id' => 'nullable|integer',
            'provider_name' => 'nullable|string|max:191',
            'session_link' => 'nullable|url|max:500',
            'scheduled_at' => 'required|date',
            'status' => 'nullable|string|in:planned,in_progress,completed,cancelled',
            'summary' => 'nullable|string',
        ]);

        $patient = Patient::where('created_by', $creatorId)->findOrFail($data['patient_id']);
        if (!empty($data['appointment_id'])) {
            MedicalAppointment::where('created_by', $creatorId)->findOrFail($data['appointment_id']);
        }

        $data['status'] = $data['status'] ?? 'planned';
        $data['created_by'] = $creatorId;
        TelemedicineSession::create($data);

        $this->securityAccess->logSensitiveAccess('create_telemedicine_session', Patient::class, $patient->id, [
            'patient_name' => trim($patient->first_name . ' ' . $patient->last_name),
        ]);

        return redirect()->route('medical.operations.index')->with('success', __('Telemedicine session saved.'));
    }

    public function storeLabOrder(Request $request)
    {
        if (!\Auth::user()->can('create medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'patient_id' => 'required|integer',
            'consultation_id' => 'nullable|integer',
            'panel_name' => 'required|string|max:191',
            'sample_type' => 'nullable|string|max:64',
            'status' => 'nullable|string|in:ordered,collected,validated,completed',
            'critical_flag' => 'nullable|boolean',
            'ordered_at' => 'required|date',
            'collected_at' => 'nullable|date|after_or_equal:ordered_at',
            'validated_at' => 'nullable|date|after_or_equal:ordered_at',
            'result_summary' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $patient = Patient::where('created_by', $creatorId)->findOrFail($data['patient_id']);
        if (!empty($data['consultation_id'])) {
            PatientConsultation::where('created_by', $creatorId)->findOrFail($data['consultation_id']);
        }

        $data['status'] = $data['status'] ?? 'ordered';
        $data['critical_flag'] = (bool) ($data['critical_flag'] ?? false);
        $data['created_by'] = $creatorId;
        $labOrder = LabOrder::create($data);
        $this->auditTrail->record('medical_lab_order_created', [
            'auditable' => $labOrder,
            'new_values' => $labOrder->only(['patient_id', 'panel_name', 'status', 'critical_flag']),
        ]);

        $this->securityAccess->logSensitiveAccess('create_lab_order', Patient::class, $patient->id, [
            'patient_name' => trim($patient->first_name . ' ' . $patient->last_name),
        ]);

        return redirect()->route('medical.operations.index')->with('success', __('Lab order saved.'));
    }

    public function storeSurgicalProcedure(Request $request)
    {
        if (!\Auth::user()->can('create medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'patient_id' => 'required|integer',
            'hospital_admission_id' => 'nullable|integer',
            'procedure_name' => 'required|string|max:191',
            'surgeon_name' => 'nullable|string|max:191',
            'theatre_name' => 'nullable|string|max:191',
            'scheduled_at' => 'required|date',
            'status' => 'nullable|string|in:planned,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $patient = Patient::where('created_by', $creatorId)->findOrFail($data['patient_id']);
        if (!empty($data['hospital_admission_id'])) {
            HospitalAdmission::where('created_by', $creatorId)->findOrFail($data['hospital_admission_id']);
        }

        $data['status'] = $data['status'] ?? 'planned';
        $data['created_by'] = $creatorId;
        $procedure = SurgicalProcedure::create($data);
        $this->auditTrail->record('medical_surgical_procedure_created', [
            'auditable' => $procedure,
            'new_values' => $procedure->only(['patient_id', 'procedure_name', 'status', 'scheduled_at']),
        ]);

        $this->securityAccess->logSensitiveAccess('create_surgical_procedure', Patient::class, $patient->id, [
            'patient_name' => trim($patient->first_name . ' ' . $patient->last_name),
        ]);

        return redirect()->route('medical.operations.index')->with('success', __('Surgical procedure recorded.'));
    }

    public function storeBiomedicalAsset(Request $request)
    {
        if (!\Auth::user()->can('create medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'asset_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('biomedical_assets', 'asset_code')->where(fn ($query) => $query->where('created_by', $creatorId)),
            ],
            'equipment_type' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:191',
            'calibration_due_date' => 'nullable|date',
            'maintenance_status' => 'nullable|string|in:operational,maintenance,due,out_of_service',
            'notes' => 'nullable|string',
        ]);

        $data['maintenance_status'] = $data['maintenance_status'] ?? 'operational';
        $data['created_by'] = $creatorId;
        $asset = BiomedicalAsset::create($data);
        $this->auditTrail->record('biomedical_asset_created', [
            'auditable' => $asset,
            'new_values' => $asset->only(['name', 'asset_code', 'equipment_type', 'maintenance_status']),
        ]);

        return redirect()->route('medical.operations.index')->with('success', __('Biomedical asset saved.'));
    }

    public function storeMedicalSpecialty(Request $request)
    {
        if (!\Auth::user()->can('create medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('medical_specialties', 'code')->where(fn ($query) => $query->where('created_by', $creatorId)),
            ],
            'department_name' => 'nullable|string|max:191',
            'head_name' => 'nullable|string|max:191',
            'status' => 'nullable|string|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $data['status'] = $data['status'] ?? 'active';
        $data['created_by'] = $creatorId;
        $specialty = MedicalSpecialty::create($data);
        $this->auditTrail->record('medical_specialty_created', [
            'auditable' => $specialty,
            'new_values' => $specialty->only(['name', 'code', 'status']),
        ]);

        return redirect()->route('medical.operations.index')->with('success', __('Medical specialty saved.'));
    }

    public function reports()
    {
        if (!\Auth::user()->can('manage medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $emergencyVisits = EmergencyVisit::with('patient')->where('created_by', $creatorId)->latest('arrived_at')->limit(15)->get();
        $labOrders = LabOrder::with('patient')->where('created_by', $creatorId)->latest('ordered_at')->limit(15)->get();
        $labResults = PatientLabResult::with('patient')->where('created_by', $creatorId)->latest('result_date')->limit(15)->get();
        $surgeries = SurgicalProcedure::with('patient')->where('created_by', $creatorId)->latest('scheduled_at')->limit(15)->get();
        $telemedicineSessions = TelemedicineSession::with('patient')->where('created_by', $creatorId)->latest('scheduled_at')->limit(15)->get();
        $medicalInvoices = MedicalInvoice::with('patient')->where('created_by', $creatorId)->latest('invoice_date')->limit(15)->get();
        $biomedicalAssets = BiomedicalAsset::where('created_by', $creatorId)->get();

        $kpis = [
            'emergency_waiting' => $emergencyVisits->where('status', 'waiting')->count(),
            'critical_lab_orders' => $labOrders->where('critical_flag', true)->count(),
            'planned_surgeries' => $surgeries->where('status', 'planned')->count(),
            'telemedicine_open' => $telemedicineSessions->whereIn('status', ['planned', 'in_progress'])->count(),
            'biomedical_due' => $biomedicalAssets->filter(fn ($asset) => $asset->isDueForCalibration())->count(),
            'medical_revenue' => (float) $medicalInvoices->sum('patient_amount'),
        ];

        return view('medical.reports', compact(
            'kpis',
            'emergencyVisits',
            'labOrders',
            'labResults',
            'surgeries',
            'telemedicineSessions',
            'medicalInvoices',
            'biomedicalAssets'
        ));
    }

    public function laboratory()
    {
        if (!\Auth::user()->can('manage medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $labOrders = LabOrder::with(['patient', 'consultation'])
            ->where('created_by', $creatorId)
            ->latest('ordered_at')
            ->get();
        $labResults = PatientLabResult::with('patient')
            ->where('created_by', $creatorId)
            ->latest('result_date')
            ->limit(40)
            ->get();

        $kpis = [
            'ordered' => $labOrders->where('status', 'ordered')->count(),
            'collected' => $labOrders->where('status', 'collected')->count(),
            'validated' => $labOrders->where('status', 'validated')->count(),
            'critical' => $labOrders->where('critical_flag', true)->count(),
        ];

        return view('medical.laboratory', compact('labOrders', 'labResults', 'kpis'));
    }

    public function surgery()
    {
        if (!\Auth::user()->can('manage medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $surgeries = SurgicalProcedure::with(['patient', 'admission'])
            ->where('created_by', $creatorId)
            ->latest('scheduled_at')
            ->get();

        $kpis = [
            'planned' => $surgeries->where('status', 'planned')->count(),
            'in_progress' => $surgeries->where('status', 'in_progress')->count(),
            'completed' => $surgeries->where('status', 'completed')->count(),
            'cancelled' => $surgeries->where('status', 'cancelled')->count(),
        ];

        return view('medical.surgery', compact('surgeries', 'kpis'));
    }

    public function biomedical()
    {
        if (!\Auth::user()->can('manage medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $biomedicalAssets = BiomedicalAsset::where('created_by', $creatorId)
            ->latest('id')
            ->get();

        $kpis = [
            'operational' => $biomedicalAssets->where('maintenance_status', 'operational')->count(),
            'maintenance' => $biomedicalAssets->where('maintenance_status', 'maintenance')->count(),
            'due' => $biomedicalAssets->filter(fn ($asset) => $asset->isDueForCalibration())->count(),
            'out_of_service' => $biomedicalAssets->where('maintenance_status', 'out_of_service')->count(),
        ];

        return view('medical.biomedical', compact('biomedicalAssets', 'kpis'));
    }

    public function specialties()
    {
        if (!\Auth::user()->can('manage medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $medicalSpecialties = MedicalSpecialty::where('created_by', $creatorId)
            ->latest('id')
            ->get();
        $appointments = MedicalAppointment::where('created_by', $creatorId)
            ->latest('start_at')
            ->limit(20)
            ->get();

        return view('medical.specialties', compact('medicalSpecialties', 'appointments'));
    }

    public function updateLabOrderStatus(Request $request, LabOrder $labOrder)
    {
        if (!\Auth::user()->can('edit medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        if ((int) $labOrder->created_by !== (int) $creatorId) {
            abort(404);
        }

        $data = $request->validate([
            'status' => 'required|string|in:ordered,collected,validated,completed',
            'result_summary' => 'nullable|string',
        ]);

        $oldValues = $labOrder->only(['status', 'result_summary']);
        $labOrder->fill($data);
        if ($data['status'] === 'collected' && empty($labOrder->collected_at)) {
            $labOrder->collected_at = now();
        }
        if (in_array($data['status'], ['validated', 'completed'], true) && empty($labOrder->validated_at)) {
            $labOrder->validated_at = now();
        }
        $labOrder->save();

        $this->auditTrail->record('medical_lab_order_updated', [
            'auditable' => $labOrder,
            'old_values' => $oldValues,
            'new_values' => $labOrder->only(['status', 'result_summary']),
        ]);

        return redirect()->back()->with('success', __('Lab order updated.'));
    }

    public function updateSurgicalProcedureStatus(Request $request, SurgicalProcedure $surgicalProcedure)
    {
        if (!\Auth::user()->can('edit medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        if ((int) $surgicalProcedure->created_by !== (int) $creatorId) {
            abort(404);
        }

        $data = $request->validate([
            'status' => 'required|string|in:planned,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $surgicalProcedure->only(['status', 'notes']);
        $surgicalProcedure->update($data);

        $this->auditTrail->record('medical_surgical_procedure_updated', [
            'auditable' => $surgicalProcedure,
            'old_values' => $oldValues,
            'new_values' => $surgicalProcedure->only(['status', 'notes']),
        ]);

        return redirect()->back()->with('success', __('Surgical procedure updated.'));
    }

    public function updateBiomedicalAssetStatus(Request $request, BiomedicalAsset $biomedicalAsset)
    {
        if (!\Auth::user()->can('edit medical operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        if ((int) $biomedicalAsset->created_by !== (int) $creatorId) {
            abort(404);
        }

        $data = $request->validate([
            'maintenance_status' => 'required|string|in:operational,maintenance,due,out_of_service',
            'calibration_due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $biomedicalAsset->only(['maintenance_status', 'calibration_due_date', 'notes']);
        $biomedicalAsset->update($data);

        $this->auditTrail->record('biomedical_asset_updated', [
            'auditable' => $biomedicalAsset,
            'old_values' => $oldValues,
            'new_values' => $biomedicalAsset->only(['maintenance_status', 'calibration_due_date', 'notes']),
        ]);

        return redirect()->back()->with('success', __('Biomedical asset updated.'));
    }
}
