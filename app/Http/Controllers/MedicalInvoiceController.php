<?php

namespace App\Http\Controllers;

use App\Models\MedicalAppointment;
use App\Models\MedicalInvoice;
use App\Models\MedicalInvoiceItem;
use App\Models\MedicalRecordAccessLog;
use App\Models\MedicalService;
use App\Models\Patient;
use App\Models\PatientConsultation;
use Illuminate\Http\Request;

class MedicalInvoiceController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage medical invoice')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $invoices = MedicalInvoice::with('patient')
            ->where('created_by', \Auth::user()->creatorId())
            ->latest()
            ->get();

        return view('medical_invoice.index', compact('invoices'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create medical invoice')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $patients = Patient::where('created_by', \Auth::user()->creatorId())->orderBy('last_name')->get();
        $services = MedicalService::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get();
        $consultations = PatientConsultation::where('created_by', \Auth::user()->creatorId())->orderByDesc('consultation_date')->get();
        $appointments = MedicalAppointment::where('created_by', \Auth::user()->creatorId())->orderByDesc('start_at')->get();

        return view('medical_invoice.create', compact('patients', 'services', 'consultations', 'appointments'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create medical invoice')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $invoice = MedicalInvoice::create([
            'patient_id' => $request->patient_id,
            'appointment_id' => $request->appointment_id,
            'consultation_id' => $request->consultation_id,
            'invoice_number' => $this->nextInvoiceNumber(),
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'insurer_name' => $request->insurer_name,
            'notes' => $request->notes,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        $this->syncItems($invoice, $request->input('items', []));
        MedicalRecordAccessLog::record($invoice->patient_id, 'create_medical_invoice', 'medical-invoices.store');

        return redirect()->route('medical-invoices.show', $invoice->id)->with('success', __('Medical invoice successfully created.'));
    }

    public function show($id)
    {
        if (!\Auth::user()->can('show medical invoice')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $invoice = MedicalInvoice::with(['patient', 'consultation', 'appointment', 'items.service', 'payments'])
            ->where('created_by', \Auth::user()->creatorId())
            ->findOrFail($id);

        MedicalRecordAccessLog::record($invoice->patient_id, 'show_medical_invoice', 'medical-invoices.show');

        return view('medical_invoice.show', compact('invoice'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit medical invoice')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $invoice = MedicalInvoice::with('items')
            ->where('created_by', \Auth::user()->creatorId())
            ->findOrFail($id);
        $patients = Patient::where('created_by', \Auth::user()->creatorId())->orderBy('last_name')->get();
        $services = MedicalService::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get();
        $consultations = PatientConsultation::where('created_by', \Auth::user()->creatorId())->orderByDesc('consultation_date')->get();
        $appointments = MedicalAppointment::where('created_by', \Auth::user()->creatorId())->orderByDesc('start_at')->get();

        return view('medical_invoice.edit', compact('invoice', 'patients', 'services', 'consultations', 'appointments'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit medical invoice')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $invoice = MedicalInvoice::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $validator = \Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $invoice->update([
            'patient_id' => $request->patient_id,
            'appointment_id' => $request->appointment_id,
            'consultation_id' => $request->consultation_id,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'status' => $request->status,
            'insurer_name' => $request->insurer_name,
            'notes' => $request->notes,
        ]);

        MedicalInvoiceItem::where('medical_invoice_id', $invoice->id)->delete();
        $this->syncItems($invoice, $request->input('items', []));

        return redirect()->route('medical-invoices.show', $invoice->id)->with('success', __('Medical invoice successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete medical invoice')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $invoice = MedicalInvoice::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $invoice->items()->delete();
        $invoice->payments()->delete();
        $invoice->delete();

        return redirect()->route('medical-invoices.index')->with('success', __('Medical invoice successfully deleted.'));
    }

    protected function syncItems(MedicalInvoice $invoice, array $items)
    {
        $total = 0;
        $insuranceTotal = 0;
        $patientTotal = 0;

        foreach ($items as $item) {
            if (empty($item['description']) && empty($item['medical_service_id'])) {
                continue;
            }

            $service = null;
            if (!empty($item['medical_service_id'])) {
                $service = MedicalService::where('created_by', \Auth::user()->creatorId())->find($item['medical_service_id']);
            }

            $quantity = max(1, (float) ($item['quantity'] ?? 1));
            $unitPrice = (float) ($item['unit_price'] ?? ($service->price ?? 0));
            $coverageRate = (float) ($item['coverage_rate'] ?? ($service->default_coverage_rate ?? 0));
            $lineTotal = $quantity * $unitPrice;
            $coveredAmount = ($coverageRate / 100) * $lineTotal;
            $dueAmount = $lineTotal - $coveredAmount;

            $invoice->items()->create([
                'medical_service_id' => $service?->id,
                'description' => $item['description'] ?: $service?->name ?: __('Medical service'),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'coverage_rate' => $coverageRate,
                'covered_amount' => $coveredAmount,
                'patient_amount' => $dueAmount,
            ]);

            $total += $lineTotal;
            $insuranceTotal += $coveredAmount;
            $patientTotal += $dueAmount;
        }

        $invoice->update([
            'total_amount' => $total,
            'insurance_amount' => $insuranceTotal,
            'patient_amount' => $patientTotal,
        ]);
    }

    protected function nextInvoiceNumber()
    {
        $count = MedicalInvoice::where('created_by', \Auth::user()->creatorId())->count() + 1;

        return 'MED-' . str_pad((string) $count, 5, '0', STR_PAD_LEFT);
    }
}
