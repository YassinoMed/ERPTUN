<?php

namespace App\Http\Controllers;

use App\Models\HospitalAdmission;
use App\Models\HospitalBed;
use App\Models\HospitalRoom;
use App\Models\MedicalRecordAccessLog;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class HospitalAdmissionController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage hospital admission')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $admissions = HospitalAdmission::with(['patient', 'doctor', 'room', 'bed'])
            ->where('created_by', \Auth::user()->creatorId())
            ->latest()
            ->get();

        return view('hospital_admission.index', compact('admissions'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create hospital admission')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $patients = Patient::where('created_by', \Auth::user()->creatorId())->orderBy('last_name')->get();
        $doctors = User::where('created_by', \Auth::user()->creatorId())->where('type', '!=', 'client')->orderBy('name')->get();
        $rooms = HospitalRoom::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get();
        $beds = HospitalBed::where('created_by', \Auth::user()->creatorId())->where('status', 'available')->orderBy('bed_number')->get();

        return view('hospital_admission.create', compact('patients', 'doctors', 'rooms', 'beds'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create hospital admission')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'admission_date' => 'required|date',
            'discharge_date' => 'nullable|date|after_or_equal:admission_date',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $admission = HospitalAdmission::create([
            'patient_id' => $request->patient_id,
            'attending_doctor_id' => $request->attending_doctor_id,
            'room_id' => $request->room_id,
            'bed_id' => $request->bed_id,
            'admission_date' => $request->admission_date,
            'discharge_date' => $request->discharge_date,
            'status' => $request->status,
            'reason' => $request->reason,
            'diagnosis' => $request->diagnosis,
            'care_plan' => $request->care_plan,
            'discharge_summary' => $request->discharge_summary,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        $this->setBedStatus($request->bed_id, $request->status === 'discharged' ? 'available' : 'occupied');
        MedicalRecordAccessLog::record($admission->patient_id, 'create_hospital_admission', 'hospital-admissions.store');

        return redirect()->route('hospital-admissions.show', $admission->id)->with('success', __('Hospital admission successfully created.'));
    }

    public function show($id)
    {
        if (!\Auth::user()->can('show hospital admission')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $admission = HospitalAdmission::with(['patient', 'doctor', 'room', 'bed'])
            ->where('created_by', \Auth::user()->creatorId())
            ->findOrFail($id);

        return view('hospital_admission.show', compact('admission'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit hospital admission')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $admission = HospitalAdmission::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $patients = Patient::where('created_by', \Auth::user()->creatorId())->orderBy('last_name')->get();
        $doctors = User::where('created_by', \Auth::user()->creatorId())->where('type', '!=', 'client')->orderBy('name')->get();
        $rooms = HospitalRoom::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get();
        $beds = HospitalBed::where('created_by', \Auth::user()->creatorId())->orderBy('bed_number')->get();

        return view('hospital_admission.edit', compact('admission', 'patients', 'doctors', 'rooms', 'beds'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit hospital admission')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $admission = HospitalAdmission::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $validator = \Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'admission_date' => 'required|date',
            'discharge_date' => 'nullable|date|after_or_equal:admission_date',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        if ($admission->bed_id && (int) $admission->bed_id !== (int) $request->bed_id) {
            $this->setBedStatus($admission->bed_id, 'available');
        }

        $admission->update([
            'patient_id' => $request->patient_id,
            'attending_doctor_id' => $request->attending_doctor_id,
            'room_id' => $request->room_id,
            'bed_id' => $request->bed_id,
            'admission_date' => $request->admission_date,
            'discharge_date' => $request->status === 'discharged' ? ($request->discharge_date ?: now()) : $request->discharge_date,
            'status' => $request->status,
            'reason' => $request->reason,
            'diagnosis' => $request->diagnosis,
            'care_plan' => $request->care_plan,
            'discharge_summary' => $request->discharge_summary,
        ]);

        $this->setBedStatus($request->bed_id, $request->status === 'discharged' ? 'available' : 'occupied');

        return redirect()->route('hospital-admissions.show', $admission->id)->with('success', __('Hospital admission successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete hospital admission')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $admission = HospitalAdmission::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $this->setBedStatus($admission->bed_id, 'available');
        $admission->delete();

        return redirect()->route('hospital-admissions.index')->with('success', __('Hospital admission successfully deleted.'));
    }

    protected function setBedStatus($bedId, $status)
    {
        if (!$bedId) {
            return;
        }

        $bed = HospitalBed::where('created_by', \Auth::user()->creatorId())->find($bedId);
        if ($bed) {
            $bed->status = $status;
            $bed->save();
        }
    }
}
