<?php

namespace App\Http\Controllers;

use App\Models\ManagedProperty;
use App\Models\PropertyUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyUnitController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage property unit') && ! Auth::user()->can('show property unit')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $units = PropertyUnit::query()
            ->where('created_by', Auth::user()->creatorId())
            ->with('property')
            ->withCount('leases')
            ->latest('id')
            ->get();

        return view('property_units.index', compact('units'));
    }

    public function create()
    {
        if (! Auth::user()->can('create property unit')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $properties = ManagedProperty::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $statuses = PropertyUnit::$statuses;

        return view('property_units.create', compact('properties', 'statuses'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create property unit')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'managed_property_id' => 'required|integer|exists:managed_properties,id',
            'unit_code' => 'required|max:255',
            'status' => 'required',
            'area' => 'nullable|numeric|min:0',
            'monthly_rent' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        PropertyUnit::create($request->only([
            'managed_property_id',
            'unit_code',
            'floor',
            'area',
            'monthly_rent',
            'status',
            'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('property-units.index')->with('success', __('Property unit successfully created.'));
    }

    public function show(PropertyUnit $propertyUnit)
    {
        if (! $this->canAccess($propertyUnit)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $propertyUnit->load(['property', 'leases.customer']);

        return view('property_units.show', compact('propertyUnit'));
    }

    public function edit(PropertyUnit $propertyUnit)
    {
        if (! Auth::user()->can('edit property unit') || ! $this->owns($propertyUnit)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $properties = ManagedProperty::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $statuses = PropertyUnit::$statuses;

        return view('property_units.edit', compact('propertyUnit', 'properties', 'statuses'));
    }

    public function update(Request $request, PropertyUnit $propertyUnit)
    {
        if (! Auth::user()->can('edit property unit') || ! $this->owns($propertyUnit)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'managed_property_id' => 'required|integer|exists:managed_properties,id',
            'unit_code' => 'required|max:255',
            'status' => 'required',
            'area' => 'nullable|numeric|min:0',
            'monthly_rent' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $propertyUnit->update($request->only([
            'managed_property_id',
            'unit_code',
            'floor',
            'area',
            'monthly_rent',
            'status',
            'notes',
        ]));

        return redirect()->route('property-units.index')->with('success', __('Property unit successfully updated.'));
    }

    public function destroy(PropertyUnit $propertyUnit)
    {
        if (! Auth::user()->can('delete property unit') || ! $this->owns($propertyUnit)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $propertyUnit->delete();

        return redirect()->route('property-units.index')->with('success', __('Property unit successfully deleted.'));
    }

    protected function owns(PropertyUnit $propertyUnit)
    {
        return (int) $propertyUnit->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(PropertyUnit $propertyUnit)
    {
        return $this->owns($propertyUnit) && (Auth::user()->can('manage property unit') || Auth::user()->can('show property unit'));
    }
}
