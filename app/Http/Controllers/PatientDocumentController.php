<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecordAccessLog;
use App\Models\Patient;
use App\Models\PatientDocument;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientDocumentController extends Controller
{
    public function store(Request $request, $patientId)
    {
        if (!\Auth::user()->can('create patient document')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $patient = Patient::where('created_by', \Auth::user()->creatorId())->findOrFail($patientId);

        $validator = \Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'document_file' => 'required|file|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $file = $request->file('document_file');
        $fileSize = $file->getSize();
        $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $fileSize);

        if ($result !== 1) {
            return redirect()->back()->with('error', __('Storage limit exceeded.'));
        }

        $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $filePath = $file->storeAs('patient-documents', $fileName);

        PatientDocument::create([
            'patient_id' => $patient->id,
            'category' => $request->category,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'uploaded_at' => now(),
            'created_by' => \Auth::user()->creatorId(),
        ]);

        MedicalRecordAccessLog::record($patient->id, 'upload_document', 'patients.documents.store');

        return redirect()->route('patients.show', $patient->id)->with('success', __('Patient document successfully uploaded.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete patient document')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $document = PatientDocument::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $patientId = $document->patient_id;

        if ($document->file_path) {
            Storage::delete($document->file_path);
        }

        $document->delete();
        MedicalRecordAccessLog::record($patientId, 'delete_document', 'patient-documents.destroy');

        return redirect()->route('patients.show', $patientId)->with('success', __('Patient document successfully deleted.'));
    }
}
