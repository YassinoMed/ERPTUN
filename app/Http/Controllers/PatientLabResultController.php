<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientLabResult;
use Illuminate\Http\Request;

class PatientLabResultController extends Controller
{
    public function store(Request $request, $patientId)
    {
        if (!\Auth::user()->can('create patient lab result')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $patient = Patient::where('created_by', \Auth::user()->creatorId())->findOrFail($patientId);

        $validator = \Validator::make($request->all(), [
            'test_name' => 'required|string|max:255',
            'result_value' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'reference_range' => 'nullable|string|max:255',
            'result_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $result = new PatientLabResult();
        $result->patient_id = $patient->id;
        $result->consultation_id = $request->consultation_id;
        $result->test_name = $request->test_name;
        $result->result_value = $request->result_value;
        $result->unit = $request->unit;
        $result->reference_range = $request->reference_range;
        $result->result_date = $request->result_date;
        $result->notes = $request->notes;
        $result->created_by = \Auth::user()->creatorId();
        $result->save();

        return redirect()->route('patients.show', $patient->id)->with('success', __('Lab result successfully added.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete patient lab result')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $result = PatientLabResult::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $patientId = $result->patient_id;
        $result->delete();

        return redirect()->route('patients.show', $patientId)->with('success', __('Lab result successfully deleted.'));
    }
}
