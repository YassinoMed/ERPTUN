<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MedicalRecordAccessLog;
use App\Models\Patient;
use App\Models\PatientConsultation;
use App\Models\PatientConsent;
use App\Models\PatientDocument;
use App\Models\PatientLabResult;
use App\Models\Utility;
use App\Services\Core\SecurityAccessService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(
        private readonly SecurityAccessService $securityAccess
    ) {
    }

    public function index(Request $request)
    {
        if (!\Auth::user()->can('manage patient')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $query = Patient::where('created_by', \Auth::user()->creatorId())->whereNull('archived_at');
        $search = trim($request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('cin', 'like', '%' . $search . '%')
                    ->orWhere('cnam_number', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        $patients = $query->orderBy('id', 'desc')->get();

        return view('patient.index', compact('patients', 'search'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create patient')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $customers = Customer::where('created_by', \Auth::user()->creatorId())->whereNull('archived_at')->get()->pluck('name', 'id');

        return view('patient.create', compact('customers'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create patient')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cin' => 'nullable|string|max:255',
            'cnam_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $patient = new Patient();
        $patient->customer_id = $request->customer_id;
        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->cin = $request->cin;
        $patient->cnam_number = $request->cnam_number;
        $patient->gender = $request->gender;
        $patient->blood_group = $request->blood_group;
        $patient->birth_date = $request->birth_date;
        $patient->phone = $request->phone;
        $patient->email = $request->email;
        $patient->address = $request->address;
        $patient->allergies = $request->allergies;
        $patient->medical_history = $request->medical_history;
        $patient->current_treatments = $request->current_treatments;
        $patient->emergency_contact_name = $request->emergency_contact_name;
        $patient->emergency_contact_phone = $request->emergency_contact_phone;
        $patient->emergency_contact_relationship = $request->emergency_contact_relationship;
        $patient->created_by = \Auth::user()->creatorId();

        if ($request->hasFile('photo')) {
            $imageSize = $request->file('photo')->getSize();
            $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $imageSize);
            if ($result == 1) {
                $imageName = time() . '.' . $request->photo->extension();
                $request->file('photo')->storeAs('patients', $imageName);
                $patient->photo_path = 'patients/' . $imageName;
            }
        }

        $patient->save();

        return redirect()->route('patients.index')->with('success', __('Patient successfully created.'));
    }

    public function show($id)
    {
        if (!\Auth::user()->can('show patient')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $patient = Patient::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $consultations = PatientConsultation::where('created_by', \Auth::user()->creatorId())
            ->where('patient_id', $patient->id)
            ->with(['prescriptions', 'labResults'])
            ->orderBy('consultation_date', 'desc')
            ->get();
        $labResults = PatientLabResult::where('created_by', \Auth::user()->creatorId())
            ->where('patient_id', $patient->id)
            ->orderBy('result_date', 'desc')
            ->get();
        $documents = PatientDocument::where('created_by', \Auth::user()->creatorId())
            ->where('patient_id', $patient->id)
            ->orderBy('uploaded_at', 'desc')
            ->get();
        $consents = PatientConsent::where('created_by', \Auth::user()->creatorId())
            ->where('patient_id', $patient->id)
            ->orderBy('consented_at', 'desc')
            ->get();
        $accessLogs = MedicalRecordAccessLog::with('user')
            ->where('created_by', \Auth::user()->creatorId())
            ->where('patient_id', $patient->id)
            ->latest()
            ->limit(10)
            ->get();

        MedicalRecordAccessLog::record($patient->id, 'view_record', 'patient.show');
        $this->securityAccess->logSensitiveAccess('view_patient_record', Patient::class, $patient->id, [
            'patient_name' => trim($patient->first_name.' '.$patient->last_name),
        ]);

        return view('patient.show', compact('patient', 'consultations', 'labResults', 'documents', 'consents', 'accessLogs'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit patient')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $patient = Patient::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $customers = Customer::where('created_by', \Auth::user()->creatorId())->whereNull('archived_at')->get()->pluck('name', 'id');

        return view('patient.edit', compact('patient', 'customers'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit patient')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $patient = Patient::where('created_by', \Auth::user()->creatorId())->findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'cin' => 'nullable|string|max:255',
            'cnam_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $patient->customer_id = $request->customer_id;
        $patient->first_name = $request->first_name;
        $patient->last_name = $request->last_name;
        $patient->cin = $request->cin;
        $patient->cnam_number = $request->cnam_number;
        $patient->gender = $request->gender;
        $patient->blood_group = $request->blood_group;
        $patient->birth_date = $request->birth_date;
        $patient->phone = $request->phone;
        $patient->email = $request->email;
        $patient->address = $request->address;
        $patient->allergies = $request->allergies;
        $patient->medical_history = $request->medical_history;
        $patient->current_treatments = $request->current_treatments;
        $patient->emergency_contact_name = $request->emergency_contact_name;
        $patient->emergency_contact_phone = $request->emergency_contact_phone;
        $patient->emergency_contact_relationship = $request->emergency_contact_relationship;

        if ($request->hasFile('photo')) {
            $imageSize = $request->file('photo')->getSize();
            $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $imageSize);
            if ($result == 1) {
                $imageName = time() . '.' . $request->photo->extension();
                $request->file('photo')->storeAs('patients', $imageName);
                $patient->photo_path = 'patients/' . $imageName;
            }
        }

        $patient->save();

        return redirect()->route('patients.index')->with('success', __('Patient successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete patient')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $patient = Patient::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $patient->delete();

        return redirect()->route('patients.index')->with('success', __('Patient successfully deleted.'));
    }
}
