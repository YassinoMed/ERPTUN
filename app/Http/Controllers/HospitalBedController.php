<?php

namespace App\Http\Controllers;

use App\Models\HospitalBed;
use App\Models\HospitalRoom;
use Illuminate\Http\Request;

class HospitalBedController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage hospital bed')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $beds = HospitalBed::with('room')->where('created_by', \Auth::user()->creatorId())->latest()->get();

        return view('hospital_bed.index', compact('beds'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create hospital bed')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $rooms = HospitalRoom::where('created_by', \Auth::user()->creatorId())->orderBy('name')->pluck('name', 'id');

        return view('hospital_bed.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create hospital bed')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'hospital_room_id' => 'required|integer',
            'bed_number' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        HospitalBed::create($request->only('hospital_room_id', 'bed_number', 'status') + ['created_by' => \Auth::user()->creatorId()]);

        return redirect()->route('hospital-beds.index')->with('success', __('Hospital bed successfully created.'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit hospital bed')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $bed = HospitalBed::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $rooms = HospitalRoom::where('created_by', \Auth::user()->creatorId())->orderBy('name')->pluck('name', 'id');

        return view('hospital_bed.edit', compact('bed', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit hospital bed')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $bed = HospitalBed::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $validator = \Validator::make($request->all(), [
            'hospital_room_id' => 'required|integer',
            'bed_number' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $bed->update($request->only('hospital_room_id', 'bed_number', 'status'));

        return redirect()->route('hospital-beds.index')->with('success', __('Hospital bed successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete hospital bed')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $bed = HospitalBed::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $bed->delete();

        return redirect()->route('hospital-beds.index')->with('success', __('Hospital bed successfully deleted.'));
    }
}
