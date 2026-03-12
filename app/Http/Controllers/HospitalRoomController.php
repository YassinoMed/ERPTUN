<?php

namespace App\Http\Controllers;

use App\Models\HospitalRoom;
use Illuminate\Http\Request;

class HospitalRoomController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage hospital room')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $rooms = HospitalRoom::withCount('beds')->where('created_by', \Auth::user()->creatorId())->latest()->get();

        return view('hospital_room.index', compact('rooms'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create hospital room')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('hospital_room.create');
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create hospital room')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        HospitalRoom::create($request->only('name', 'department', 'room_type', 'status') + ['created_by' => \Auth::user()->creatorId()]);

        return redirect()->route('hospital-rooms.index')->with('success', __('Hospital room successfully created.'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit hospital room')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $room = HospitalRoom::where('created_by', \Auth::user()->creatorId())->findOrFail($id);

        return view('hospital_room.edit', compact('room'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit hospital room')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $room = HospitalRoom::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'room_type' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $room->update($request->only('name', 'department', 'room_type', 'status'));

        return redirect()->route('hospital-rooms.index')->with('success', __('Hospital room successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete hospital room')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $room = HospitalRoom::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        if ($room->beds()->count() > 0) {
            return redirect()->back()->with('error', __('Delete room beds first.'));
        }
        $room->delete();

        return redirect()->route('hospital-rooms.index')->with('success', __('Hospital room successfully deleted.'));
    }
}
