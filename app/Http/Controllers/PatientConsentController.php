<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecordAccessLog;
use App\Models\Patient;
use App\Models\PatientConsent;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientConsentController extends Controller
{
    public function store(Request $request, $patientId)
    {
        if (!\Auth::user()->can('create patient consent')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $patient = Patient::where('created_by', \Auth::user()->creatorId())->findOrFail($patientId);

        $validator = \Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'consented_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:consented_at',
            'notes' => 'nullable|string',
            'consent_file' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $filePath = null;
        if ($request->hasFile('consent_file')) {
            $file = $request->file('consent_file');
            $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $file->getSize());

            if ($result !== 1) {
                return redirect()->back()->with('error', __('Storage limit exceeded.'));
            }

            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('patient-consents', $fileName);
        }

        PatientConsent::create([
            'patient_id' => $patient->id,
            'title' => $request->title,
            'status' => $request->status,
            'consented_at' => $request->consented_at,
            'expires_at' => $request->expires_at,
            'notes' => $request->notes,
            'file_path' => $filePath,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        MedicalRecordAccessLog::record($patient->id, 'record_consent', 'patients.consents.store');

        return redirect()->route('patients.show', $patient->id)->with('success', __('Patient consent successfully saved.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete patient consent')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $consent = PatientConsent::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $patientId = $consent->patient_id;

        if ($consent->file_path) {
            Storage::delete($consent->file_path);
        }

        $consent->delete();
        MedicalRecordAccessLog::record($patientId, 'delete_consent', 'patient-consents.destroy');

        return redirect()->route('patients.show', $patientId)->with('success', __('Patient consent successfully deleted.'));
    }
}
