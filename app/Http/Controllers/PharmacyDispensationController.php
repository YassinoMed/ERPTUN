<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecordAccessLog;
use App\Models\Patient;
use App\Models\PatientConsultation;
use App\Models\PatientPrescription;
use App\Models\PharmacyDispensation;
use App\Models\PharmacyDispensationItem;
use App\Models\PharmacyMedication;
use Illuminate\Http\Request;

class PharmacyDispensationController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage pharmacy dispensation')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $dispensations = PharmacyDispensation::with('patient')
            ->where('created_by', \Auth::user()->creatorId())
            ->latest()
            ->get();

        return view('pharmacy_dispensation.index', compact('dispensations'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create pharmacy dispensation')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $patients = Patient::where('created_by', \Auth::user()->creatorId())->orderBy('last_name')->get();
        $consultations = PatientConsultation::where('created_by', \Auth::user()->creatorId())->orderByDesc('consultation_date')->get();
        $prescriptions = PatientPrescription::where('created_by', \Auth::user()->creatorId())->latest()->get();
        $medications = PharmacyMedication::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get();

        return view('pharmacy_dispensation.create', compact('patients', 'consultations', 'prescriptions', 'medications'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create pharmacy dispensation')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'dispensed_at' => 'required|date',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $dispensation = PharmacyDispensation::create([
            'patient_id' => $request->patient_id,
            'consultation_id' => $request->consultation_id,
            'prescription_id' => $request->prescription_id,
            'dispensed_by' => \Auth::id(),
            'dispensed_at' => $request->dispensed_at,
            'status' => $request->status,
            'notes' => $request->notes,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        $this->syncItems($dispensation, $request->input('items', []), false);
        MedicalRecordAccessLog::record($dispensation->patient_id, 'create_pharmacy_dispensation', 'pharmacy-dispensations.store');

        return redirect()->route('pharmacy-dispensations.show', $dispensation->id)->with('success', __('Pharmacy dispensation successfully created.'));
    }

    public function show($id)
    {
        if (!\Auth::user()->can('show pharmacy dispensation')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $dispensation = PharmacyDispensation::with(['patient', 'consultation', 'prescription', 'dispenser', 'items.medication'])
            ->where('created_by', \Auth::user()->creatorId())
            ->findOrFail($id);

        return view('pharmacy_dispensation.show', compact('dispensation'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit pharmacy dispensation')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $dispensation = PharmacyDispensation::with('items')
            ->where('created_by', \Auth::user()->creatorId())
            ->findOrFail($id);
        $patients = Patient::where('created_by', \Auth::user()->creatorId())->orderBy('last_name')->get();
        $consultations = PatientConsultation::where('created_by', \Auth::user()->creatorId())->orderByDesc('consultation_date')->get();
        $prescriptions = PatientPrescription::where('created_by', \Auth::user()->creatorId())->latest()->get();
        $medications = PharmacyMedication::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get();

        return view('pharmacy_dispensation.edit', compact('dispensation', 'patients', 'consultations', 'prescriptions', 'medications'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit pharmacy dispensation')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $dispensation = PharmacyDispensation::with('items')
            ->where('created_by', \Auth::user()->creatorId())
            ->findOrFail($id);
        $validator = \Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'dispensed_at' => 'required|date',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $this->syncItems($dispensation, $dispensation->items->toArray(), true);
        PharmacyDispensationItem::where('pharmacy_dispensation_id', $dispensation->id)->delete();

        $dispensation->update([
            'patient_id' => $request->patient_id,
            'consultation_id' => $request->consultation_id,
            'prescription_id' => $request->prescription_id,
            'dispensed_at' => $request->dispensed_at,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        $this->syncItems($dispensation, $request->input('items', []), false);

        return redirect()->route('pharmacy-dispensations.show', $dispensation->id)->with('success', __('Pharmacy dispensation successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete pharmacy dispensation')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $dispensation = PharmacyDispensation::with('items')
            ->where('created_by', \Auth::user()->creatorId())
            ->findOrFail($id);

        $this->syncItems($dispensation, $dispensation->items->toArray(), true);
        $dispensation->items()->delete();
        $dispensation->delete();

        return redirect()->route('pharmacy-dispensations.index')->with('success', __('Pharmacy dispensation successfully deleted.'));
    }

    protected function syncItems(PharmacyDispensation $dispensation, array $items, $restoreOnly)
    {
        foreach ($items as $item) {
            $medicationId = $item['pharmacy_medication_id'] ?? null;
            $quantity = (float) ($item['quantity'] ?? 0);
            if (!$medicationId || $quantity <= 0) {
                continue;
            }

            $medication = PharmacyMedication::where('created_by', \Auth::user()->creatorId())->find($medicationId);
            if (!$medication) {
                continue;
            }

            $medication->stock_quantity = $restoreOnly
                ? $medication->stock_quantity + $quantity
                : max(0, $medication->stock_quantity - $quantity);
            $medication->save();

            if ($restoreOnly) {
                continue;
            }

            $dispensation->items()->create([
                'pharmacy_medication_id' => $medicationId,
                'quantity' => $quantity,
                'dosage' => $item['dosage'] ?? null,
                'frequency' => $item['frequency'] ?? null,
                'duration' => $item['duration'] ?? null,
                'notes' => $item['notes'] ?? null,
            ]);
        }
    }
}
