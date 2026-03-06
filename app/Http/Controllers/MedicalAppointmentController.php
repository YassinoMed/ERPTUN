<?php

namespace App\Http\Controllers;

use App\Models\MedicalAppointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class MedicalAppointmentController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage medical appointment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $appointments = MedicalAppointment::with(['patient', 'doctor'])
            ->where('created_by', \Auth::user()->creatorId())
            ->orderBy('start_at', 'desc')
            ->get();

        return view('medical_appointment.index', compact('appointments'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create medical appointment')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $patients = Patient::where('created_by', \Auth::user()->creatorId())->orderBy('last_name')->get();
        $doctors = User::where('created_by', \Auth::user()->creatorId())->where('type', '!=', 'client')->orderBy('name')->get();

        return view('medical_appointment.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create medical appointment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'doctor_id' => 'nullable|integer',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'room' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'reminder_channel' => 'nullable|string|max:50',
            'reminder_at' => 'nullable|date|before:start_at',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $overlapQuery = MedicalAppointment::where('created_by', \Auth::user()->creatorId())
            ->where('status', '!=', 'canceled')
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_at', [$request->start_at, $request->end_at])
                    ->orWhereBetween('end_at', [$request->start_at, $request->end_at])
                    ->orWhere(function ($q2) use ($request) {
                        $q2->where('start_at', '<=', $request->start_at)
                            ->where('end_at', '>=', $request->end_at);
                    });
            });

        if ($request->doctor_id) {
            $overlapQuery->where('doctor_id', $request->doctor_id);
        }

        if ($request->room) {
            $overlapQuery->orWhere(function ($q) use ($request) {
                $q->where('room', $request->room)
                    ->where('created_by', \Auth::user()->creatorId())
                    ->where('status', '!=', 'canceled')
                    ->where(function ($q2) use ($request) {
                        $q2->whereBetween('start_at', [$request->start_at, $request->end_at])
                            ->orWhereBetween('end_at', [$request->start_at, $request->end_at])
                            ->orWhere(function ($q3) use ($request) {
                                $q3->where('start_at', '<=', $request->start_at)
                                    ->where('end_at', '>=', $request->end_at);
                            });
                    });
            });
        }

        if ($overlapQuery->exists()) {
            return redirect()->back()->with('error', __('Double booking detected for doctor or room.'));
        }

        $appointment = new MedicalAppointment();
        $appointment->patient_id = $request->patient_id;
        $appointment->doctor_id = $request->doctor_id;
        $appointment->room = $request->room;
        $appointment->specialty = $request->specialty;
        $appointment->start_at = $request->start_at;
        $appointment->end_at = $request->end_at;
        $appointment->status = 'scheduled';
        $appointment->reminder_channel = $request->reminder_channel;
        if ($request->reminder_channel) {
            $appointment->reminder_at = $request->reminder_at
                ? \Carbon\Carbon::parse($request->reminder_at)
                : \Carbon\Carbon::parse($request->start_at)->subHours(24);
        }
        $appointment->created_by = \Auth::user()->creatorId();
        $appointment->save();

        return redirect()->route('medical-appointments.index')->with('success', __('Appointment successfully created.'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit medical appointment')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $appointment = MedicalAppointment::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $patients = Patient::where('created_by', \Auth::user()->creatorId())->orderBy('last_name')->get();
        $doctors = User::where('created_by', \Auth::user()->creatorId())->where('type', '!=', 'client')->orderBy('name')->get();

        return view('medical_appointment.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit medical appointment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $appointment = MedicalAppointment::where('created_by', \Auth::user()->creatorId())->findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'doctor_id' => 'nullable|integer',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'room' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'status' => 'required|string',
            'reminder_channel' => 'nullable|string|max:50',
            'reminder_at' => 'nullable|date|before:start_at',
            'cancel_reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $overlapQuery = MedicalAppointment::where('created_by', \Auth::user()->creatorId())
            ->where('status', '!=', 'canceled')
            ->where('id', '!=', $appointment->id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_at', [$request->start_at, $request->end_at])
                    ->orWhereBetween('end_at', [$request->start_at, $request->end_at])
                    ->orWhere(function ($q2) use ($request) {
                        $q2->where('start_at', '<=', $request->start_at)
                            ->where('end_at', '>=', $request->end_at);
                    });
            });

        if ($request->doctor_id) {
            $overlapQuery->where('doctor_id', $request->doctor_id);
        }

        if ($request->room) {
            $overlapQuery->orWhere(function ($q) use ($request) {
                $q->where('room', $request->room)
                    ->where('created_by', \Auth::user()->creatorId())
                    ->where('status', '!=', 'canceled')
                    ->where(function ($q2) use ($request) {
                        $q2->whereBetween('start_at', [$request->start_at, $request->end_at])
                            ->orWhereBetween('end_at', [$request->start_at, $request->end_at])
                            ->orWhere(function ($q3) use ($request) {
                                $q3->where('start_at', '<=', $request->start_at)
                                    ->where('end_at', '>=', $request->end_at);
                            });
                    });
            });
        }

        if ($overlapQuery->exists()) {
            return redirect()->back()->with('error', __('Double booking detected for doctor or room.'));
        }

        $appointment->patient_id = $request->patient_id;
        $appointment->doctor_id = $request->doctor_id;
        $appointment->room = $request->room;
        $appointment->specialty = $request->specialty;
        $appointment->start_at = $request->start_at;
        $appointment->end_at = $request->end_at;
        $appointment->status = $request->status;
        $appointment->reminder_channel = $request->reminder_channel;
        if ($request->reminder_channel) {
            $appointment->reminder_at = $request->reminder_at
                ? \Carbon\Carbon::parse($request->reminder_at)
                : \Carbon\Carbon::parse($request->start_at)->subHours(24);
        } else {
            $appointment->reminder_at = null;
        }
        $appointment->cancel_reason = $request->cancel_reason;
        $appointment->save();

        return redirect()->route('medical-appointments.index')->with('success', __('Appointment successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete medical appointment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $appointment = MedicalAppointment::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $appointment->delete();

        return redirect()->route('medical-appointments.index')->with('success', __('Appointment successfully deleted.'));
    }

    public function cancel(Request $request, $id)
    {
        if (!\Auth::user()->can('edit medical appointment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $appointment = MedicalAppointment::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $appointment->status = 'canceled';
        $appointment->canceled_at = now();
        $appointment->cancel_reason = $request->cancel_reason;
        $appointment->save();

        return redirect()->route('medical-appointments.index')->with('success', __('Appointment successfully canceled.'));
    }
}
