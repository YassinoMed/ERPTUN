<?php

namespace App\Http\Controllers;

use App\Models\BoardMeeting;
use App\Models\BoardMeetingEmployee;
use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardMeetingController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage board meeting') && !\Auth::user()->can('show board meeting')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if (Auth::user()->type == 'Employee' && !Auth::user()->can('manage board meeting')) {
            $currentEmployee = Employee::where('user_id', Auth::user()->id)->first();

            $meetings = BoardMeeting::query()
                ->where('created_by', Auth::user()->creatorId())
                ->whereHas('attendees', function ($query) use ($currentEmployee) {
                    $query->where('employee_id', optional($currentEmployee)->id);
                })
                ->with(['branch'])
                ->withCount('attendees')
                ->latest('id')
                ->get();
        } else {
            $meetings = BoardMeeting::where('created_by', Auth::user()->creatorId())
                ->with(['branch'])
                ->withCount('attendees')
                ->latest('id')
                ->get();
        }

        return view('board_meeting.index', compact('meetings'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create board meeting')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $branches = Branch::where('created_by', Auth::user()->creatorId())->get();
        $employees = Employee::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $statuses = BoardMeeting::statuses();

        return view('board_meeting.create', compact('branches', 'employees', 'statuses'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create board meeting')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'branch_id' => 'required',
            'title' => 'required|max:255',
            'meeting_date' => 'required|date',
            'meeting_time' => 'required',
            'status' => 'required',
            'attendee_ids' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $meeting = new BoardMeeting();
        $meeting->branch_id = $request->branch_id;
        $meeting->title = $request->title;
        $meeting->meeting_date = $request->meeting_date;
        $meeting->meeting_time = $request->meeting_time;
        $meeting->status = $request->status;
        $meeting->location = $request->location;
        $meeting->meeting_link = $request->meeting_link;
        $meeting->agenda = $request->agenda;
        $meeting->minutes = $request->minutes;
        $meeting->resolution_summary = $request->resolution_summary;
        $meeting->external_guests = $request->external_guests;
        $meeting->created_by = Auth::user()->creatorId();
        $meeting->save();

        foreach ($request->attendee_ids as $employeeId) {
            BoardMeetingEmployee::create([
                'board_meeting_id' => $meeting->id,
                'employee_id' => $employeeId,
                'created_by' => Auth::user()->creatorId(),
            ]);
        }

        return redirect()->route('board-meeting.index')->with('success', __('Board meeting successfully created.'));
    }

    public function show(BoardMeeting $boardMeeting)
    {
        if (!$this->canAccessMeeting($boardMeeting)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $boardMeeting->load(['branch', 'attendees.employee']);

        return view('board_meeting.show', compact('boardMeeting'));
    }

    public function edit(BoardMeeting $boardMeeting)
    {
        if (!\Auth::user()->can('edit board meeting') || !$this->ownsMeeting($boardMeeting)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $branches = Branch::where('created_by', Auth::user()->creatorId())->get();
        $employees = Employee::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $statuses = BoardMeeting::statuses();
        $selectedEmployees = $boardMeeting->attendees()->pluck('employee_id')->toArray();

        return view('board_meeting.edit', compact('boardMeeting', 'branches', 'employees', 'statuses', 'selectedEmployees'));
    }

    public function update(Request $request, BoardMeeting $boardMeeting)
    {
        if (!\Auth::user()->can('edit board meeting') || !$this->ownsMeeting($boardMeeting)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'branch_id' => 'required',
            'title' => 'required|max:255',
            'meeting_date' => 'required|date',
            'meeting_time' => 'required',
            'status' => 'required',
            'attendee_ids' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $boardMeeting->branch_id = $request->branch_id;
        $boardMeeting->title = $request->title;
        $boardMeeting->meeting_date = $request->meeting_date;
        $boardMeeting->meeting_time = $request->meeting_time;
        $boardMeeting->status = $request->status;
        $boardMeeting->location = $request->location;
        $boardMeeting->meeting_link = $request->meeting_link;
        $boardMeeting->agenda = $request->agenda;
        $boardMeeting->minutes = $request->minutes;
        $boardMeeting->resolution_summary = $request->resolution_summary;
        $boardMeeting->external_guests = $request->external_guests;
        $boardMeeting->save();

        BoardMeetingEmployee::where('board_meeting_id', $boardMeeting->id)->delete();

        foreach ($request->attendee_ids as $employeeId) {
            BoardMeetingEmployee::create([
                'board_meeting_id' => $boardMeeting->id,
                'employee_id' => $employeeId,
                'created_by' => Auth::user()->creatorId(),
            ]);
        }

        return redirect()->route('board-meeting.index')->with('success', __('Board meeting successfully updated.'));
    }

    public function destroy(BoardMeeting $boardMeeting)
    {
        if (!\Auth::user()->can('delete board meeting') || !$this->ownsMeeting($boardMeeting)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        BoardMeetingEmployee::where('board_meeting_id', $boardMeeting->id)->delete();
        $boardMeeting->delete();

        return redirect()->route('board-meeting.index')->with('success', __('Board meeting successfully deleted.'));
    }

    protected function ownsMeeting(BoardMeeting $boardMeeting)
    {
        return (int) $boardMeeting->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccessMeeting(BoardMeeting $boardMeeting)
    {
        if ($this->ownsMeeting($boardMeeting) && (Auth::user()->can('manage board meeting') || Auth::user()->can('show board meeting'))) {
            return true;
        }

        if (Auth::user()->type !== 'Employee' || !Auth::user()->can('show board meeting')) {
            return false;
        }

        $currentEmployee = Employee::where('user_id', Auth::user()->id)->first();
        if (!$currentEmployee) {
            return false;
        }

        return BoardMeetingEmployee::where('board_meeting_id', $boardMeeting->id)
            ->where('employee_id', $currentEmployee->id)
            ->exists();
    }
}
