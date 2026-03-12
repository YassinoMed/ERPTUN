<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ManagedProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagedPropertyController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage managed property') && ! Auth::user()->can('show managed property')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $properties = ManagedProperty::query()
            ->where('created_by', Auth::user()->creatorId())
            ->with('manager')
            ->withCount(['units', 'leases'])
            ->latest('id')
            ->get();

        return view('managed_properties.index', compact('properties'));
    }

    public function create()
    {
        if (! Auth::user()->can('create managed property')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $employees = Employee::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $statuses = ManagedProperty::$statuses;

        return view('managed_properties.create', compact('employees', 'statuses'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create managed property')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'property_code' => 'required|max:255',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        ManagedProperty::create($request->only([
            'name',
            'property_code',
            'property_type',
            'status',
            'manager_employee_id',
            'country',
            'city',
            'address',
            'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('managed-properties.index')->with('success', __('Property successfully created.'));
    }

    public function show(ManagedProperty $managedProperty)
    {
        if (! $this->canAccess($managedProperty)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $managedProperty->load(['manager', 'units', 'leases.customer', 'leases.unit']);

        return view('managed_properties.show', compact('managedProperty'));
    }

    public function edit(ManagedProperty $managedProperty)
    {
        if (! Auth::user()->can('edit managed property') || ! $this->owns($managedProperty)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $employees = Employee::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $statuses = ManagedProperty::$statuses;

        return view('managed_properties.edit', compact('managedProperty', 'employees', 'statuses'));
    }

    public function update(Request $request, ManagedProperty $managedProperty)
    {
        if (! Auth::user()->can('edit managed property') || ! $this->owns($managedProperty)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'property_code' => 'required|max:255',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $managedProperty->update($request->only([
            'name',
            'property_code',
            'property_type',
            'status',
            'manager_employee_id',
            'country',
            'city',
            'address',
            'notes',
        ]));

        return redirect()->route('managed-properties.index')->with('success', __('Property successfully updated.'));
    }

    public function destroy(ManagedProperty $managedProperty)
    {
        if (! Auth::user()->can('delete managed property') || ! $this->owns($managedProperty)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $managedProperty->delete();

        return redirect()->route('managed-properties.index')->with('success', __('Property successfully deleted.'));
    }

    protected function owns(ManagedProperty $managedProperty)
    {
        return (int) $managedProperty->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(ManagedProperty $managedProperty)
    {
        return $this->owns($managedProperty) && (Auth::user()->can('manage managed property') || Auth::user()->can('show managed property'));
    }
}
