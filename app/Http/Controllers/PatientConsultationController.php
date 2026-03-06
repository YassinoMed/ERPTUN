<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientConsultation;
use Illuminate\Http\Request;

class PatientConsultationController extends Controller
{
    public function create($patientId)
    {
        if (!\Auth::user()->can('create patient consultation')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $patient = Patient::where('created_by', \Auth::user()->creatorId())->findOrFail($patientId);

        return view('patient.consultation_create', compact('patient'));
    }

    public function store(Request $request, $patientId)
    {
        if (!\Auth::user()->can('create patient consultation')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $patient = Patient::where('created_by', \Auth::user()->creatorId())->findOrFail($patientId);

        $validator = \Validator::make($request->all(), [
            'consultation_date' => 'required|date',
            'doctor_name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'next_visit_date' => 'nullable|date',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $consultation = new PatientConsultation();
        $consultation->patient_id = $patient->id;
        $consultation->doctor_id = \Auth::user()->id;
        $consultation->appointment_id = $request->appointment_id;
        $consultation->consultation_date = $request->consultation_date;
        $consultation->doctor_name = $request->doctor_name ?: \Auth::user()->name;
        $consultation->title = $request->title;
        $consultation->next_visit_date = $request->next_visit_date;
        $consultation->diagnosis = $request->diagnosis;
        $consultation->notes = $request->notes;
        $consultation->created_by = \Auth::user()->creatorId();
        $consultation->save();

        return redirect()->route('patients.show', $patient->id)->with('success', __('Consultation successfully created.'));
    }
}
