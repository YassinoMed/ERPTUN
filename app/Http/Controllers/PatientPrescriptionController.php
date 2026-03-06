<?php

namespace App\Http\Controllers;

use App\Models\PatientConsultation;
use App\Models\PatientPrescription;
use Illuminate\Http\Request;

class PatientPrescriptionController extends Controller
{
    public function store(Request $request, $consultationId)
    {
        if (!\Auth::user()->can('create patient prescription')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $consultation = PatientConsultation::where('created_by', \Auth::user()->creatorId())->findOrFail($consultationId);

        $validator = \Validator::make($request->all(), [
            'medication_name' => 'required|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $prescription = new PatientPrescription();
        $prescription->consultation_id = $consultation->id;
        $prescription->medication_name = $request->medication_name;
        $prescription->dosage = $request->dosage;
        $prescription->frequency = $request->frequency;
        $prescription->duration = $request->duration;
        $prescription->notes = $request->notes;
        $prescription->created_by = \Auth::user()->creatorId();
        $prescription->save();

        return redirect()->route('patients.show', $consultation->patient_id)->with('success', __('Prescription successfully added.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete patient prescription')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $prescription = PatientPrescription::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $patientId = $prescription->consultation->patient_id;
        $prescription->delete();

        return redirect()->route('patients.show', $patientId)->with('success', __('Prescription successfully deleted.'));
    }
}
