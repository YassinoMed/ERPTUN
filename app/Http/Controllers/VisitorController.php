<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitorController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage visitor') && !Auth::user()->can('show visitor')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $visitors = Visitor::where('created_by', Auth::user()->creatorId())->with('host')->latest('id')->get();

        return view('visitors.index', compact('visitors'));
    }

    public function create()
    {
        if (!Auth::user()->can('create visitor')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $employees = Employee::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $statuses = Visitor::$statuses;

        return view('visitors.create', compact('employees', 'statuses'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create visitor')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'visitor_name' => 'required|max:255',
            'email' => 'nullable|email',
            'visit_date' => 'required|date',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        Visitor::create($request->only([
            'visitor_name',
            'company_name',
            'email',
            'phone',
            'host_employee_id',
            'visit_date',
            'visit_time',
            'purpose',
            'status',
            'badge_number',
            'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('visitors.index')->with('success', __('Visitor successfully created.'));
    }

    public function show(Visitor $visitor)
    {
        if (!$this->canAccess($visitor)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $visitor->load('host');

        return view('visitors.show', compact('visitor'));
    }

    public function edit(Visitor $visitor)
    {
        if (!Auth::user()->can('edit visitor') || !$this->owns($visitor)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $employees = Employee::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $statuses = Visitor::$statuses;

        return view('visitors.edit', compact('visitor', 'employees', 'statuses'));
    }

    public function update(Request $request, Visitor $visitor)
    {
        if (!Auth::user()->can('edit visitor') || !$this->owns($visitor)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'visitor_name' => 'required|max:255',
            'email' => 'nullable|email',
            'visit_date' => 'required|date',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $visitor->update($request->only([
            'visitor_name',
            'company_name',
            'email',
            'phone',
            'host_employee_id',
            'visit_date',
            'visit_time',
            'purpose',
            'status',
            'badge_number',
            'notes',
        ]));

        return redirect()->route('visitors.index')->with('success', __('Visitor successfully updated.'));
    }

    public function destroy(Visitor $visitor)
    {
        if (!Auth::user()->can('delete visitor') || !$this->owns($visitor)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $visitor->delete();

        return redirect()->route('visitors.index')->with('success', __('Visitor successfully deleted.'));
    }

    protected function owns(Visitor $visitor)
    {
        return (int) $visitor->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(Visitor $visitor)
    {
        return $this->owns($visitor) && (Auth::user()->can('manage visitor') || Auth::user()->can('show visitor'));
    }
}
