<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ProductionShiftTeam;
use App\Models\User;
use Illuminate\Http\Request;

class ProductionShiftTeamController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (\Auth::check() && \Auth::user()->type !== 'super admin' && (int) User::show_production() !== 1) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => __('Permission denied.')], 403);
                }

                return redirect()->route('dashboard')->with('error', __('Permission denied.'));
            }

            return $next($request);
        });
    }

    public function index()
    {
        if (!\Auth::user()->can('manage production shift team')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $shiftTeams = ProductionShiftTeam::where('created_by', \Auth::user()->creatorId())->with('supervisor')->orderBy('name')->get();

        return view('production.shift_teams.index', compact('shiftTeams'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create production shift team')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $supervisors = Employee::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.shift_teams.create', compact('supervisors'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create production shift team')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'code' => 'nullable|max:255',
            'supervisor_id' => 'nullable|integer',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        ProductionShiftTeam::create($request->only(['name', 'code', 'supervisor_id', 'start_time', 'end_time', 'status', 'notes']) + [
            'created_by' => \Auth::user()->creatorId(),
        ]);

        return redirect()->route('production.shift-teams.index')->with('success', __('Shift team successfully created.'));
    }

    public function edit(ProductionShiftTeam $shiftTeam)
    {
        if (!\Auth::user()->can('edit production shift team')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        if ($shiftTeam->created_by != \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $supervisors = Employee::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.shift_teams.edit', compact('shiftTeam', 'supervisors'));
    }

    public function update(Request $request, ProductionShiftTeam $shiftTeam)
    {
        if (!\Auth::user()->can('edit production shift team')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($shiftTeam->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'code' => 'nullable|max:255',
            'supervisor_id' => 'nullable|integer',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $shiftTeam->update($request->only(['name', 'code', 'supervisor_id', 'start_time', 'end_time', 'status', 'notes']));

        return redirect()->route('production.shift-teams.index')->with('success', __('Shift team successfully updated.'));
    }

    public function destroy(ProductionShiftTeam $shiftTeam)
    {
        if (!\Auth::user()->can('delete production shift team')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($shiftTeam->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $shiftTeam->delete();

        return redirect()->route('production.shift-teams.index')->with('success', __('Shift team successfully deleted.'));
    }
}
