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
            'reason_for_visit' => 'nullable|string|max:255',
            'next_visit_date' => 'nullable|date',
            'diagnosis' => 'nullable|string',
            'temperature' => 'nullable|numeric|min:0|max:99.99',
            'heart_rate' => 'nullable|integer|min:0|max:300',
            'blood_pressure' => 'nullable|string|max:50',
            'respiratory_rate' => 'nullable|integer|min:0|max:120',
            'weight' => 'nullable|numeric|min:0|max:999.99',
            'height' => 'nullable|numeric|min:0|max:300',
            'clinical_observations' => 'nullable|string',
            'requested_exams' => 'nullable|string',
            'medical_certificate' => 'nullable|string',
            'sick_leave_start' => 'nullable|date',
            'sick_leave_end' => 'nullable|date|after_or_equal:sick_leave_start',
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
        $consultation->reason_for_visit = $request->reason_for_visit;
        $consultation->temperature = $request->temperature;
        $consultation->heart_rate = $request->heart_rate;
        $consultation->blood_pressure = $request->blood_pressure;
        $consultation->respiratory_rate = $request->respiratory_rate;
        $consultation->weight = $request->weight;
        $consultation->height = $request->height;
        $consultation->next_visit_date = $request->next_visit_date;
        $consultation->diagnosis = $request->diagnosis;
        $consultation->clinical_observations = $request->clinical_observations;
        $consultation->requested_exams = $request->requested_exams;
        $consultation->medical_certificate = $request->medical_certificate;
        $consultation->sick_leave_start = $request->sick_leave_start;
        $consultation->sick_leave_end = $request->sick_leave_end;
        $consultation->notes = $request->notes;
        $consultation->created_by = \Auth::user()->creatorId();
        $consultation->save();

        return redirect()->route('patients.show', $patient->id)->with('success', __('Consultation successfully created.'));
    }
}
