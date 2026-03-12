<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ManagedProperty;
use App\Models\PropertyLease;
use App\Models\PropertyUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyLeaseController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage property lease') && ! Auth::user()->can('show property lease')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $leases = PropertyLease::query()
            ->where('created_by', Auth::user()->creatorId())
            ->with(['property', 'unit', 'customer'])
            ->latest('id')
            ->get();

        return view('property_leases.index', compact('leases'));
    }

    public function create()
    {
        if (! Auth::user()->can('create property lease')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $properties = ManagedProperty::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $units = PropertyUnit::query()->where('created_by', Auth::user()->creatorId())->pluck('unit_code', 'id');
        $customers = Customer::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $statuses = PropertyLease::$statuses;
        $billingCycles = PropertyLease::$billingCycles;

        return view('property_leases.create', compact('properties', 'units', 'customers', 'statuses', 'billingCycles'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create property lease')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'managed_property_id' => 'required|integer|exists:managed_properties,id',
            'property_unit_id' => 'required|integer|exists:property_units,id',
            'reference' => 'required|max:255',
            'status' => 'required',
            'billing_cycle' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'rent_amount' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $lease = PropertyLease::create($request->only([
            'managed_property_id',
            'property_unit_id',
            'customer_id',
            'reference',
            'billing_cycle',
            'status',
            'start_date',
            'end_date',
            'renewal_date',
            'rent_amount',
            'deposit_amount',
            'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        $this->syncUnitStatus($lease->property_unit_id, $lease->status);

        return redirect()->route('property-leases.index')->with('success', __('Property lease successfully created.'));
    }

    public function show(PropertyLease $propertyLease)
    {
        if (! $this->canAccess($propertyLease)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $propertyLease->load(['property', 'unit', 'customer']);

        return view('property_leases.show', compact('propertyLease'));
    }

    public function edit(PropertyLease $propertyLease)
    {
        if (! Auth::user()->can('edit property lease') || ! $this->owns($propertyLease)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $properties = ManagedProperty::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $units = PropertyUnit::query()->where('created_by', Auth::user()->creatorId())->pluck('unit_code', 'id');
        $customers = Customer::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $statuses = PropertyLease::$statuses;
        $billingCycles = PropertyLease::$billingCycles;

        return view('property_leases.edit', compact('propertyLease', 'properties', 'units', 'customers', 'statuses', 'billingCycles'));
    }

    public function update(Request $request, PropertyLease $propertyLease)
    {
        if (! Auth::user()->can('edit property lease') || ! $this->owns($propertyLease)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'managed_property_id' => 'required|integer|exists:managed_properties,id',
            'property_unit_id' => 'required|integer|exists:property_units,id',
            'reference' => 'required|max:255',
            'status' => 'required',
            'billing_cycle' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'rent_amount' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $previousUnitId = $propertyLease->property_unit_id;
        $propertyLease->update($request->only([
            'managed_property_id',
            'property_unit_id',
            'customer_id',
            'reference',
            'billing_cycle',
            'status',
            'start_date',
            'end_date',
            'renewal_date',
            'rent_amount',
            'deposit_amount',
            'notes',
        ]));

        if ($previousUnitId && (int) $previousUnitId !== (int) $propertyLease->property_unit_id) {
            $this->markUnitAvailable($previousUnitId);
        }

        $this->syncUnitStatus($propertyLease->property_unit_id, $propertyLease->status);

        return redirect()->route('property-leases.index')->with('success', __('Property lease successfully updated.'));
    }

    public function destroy(PropertyLease $propertyLease)
    {
        if (! Auth::user()->can('delete property lease') || ! $this->owns($propertyLease)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $unitId = $propertyLease->property_unit_id;
        $propertyLease->delete();
        $this->markUnitAvailable($unitId);

        return redirect()->route('property-leases.index')->with('success', __('Property lease successfully deleted.'));
    }

    protected function syncUnitStatus(?int $unitId, string $leaseStatus): void
    {
        if (! $unitId) {
            return;
        }

        $unit = PropertyUnit::query()->where('created_by', Auth::user()->creatorId())->find($unitId);
        if (! $unit) {
            return;
        }

        $unit->status = in_array($leaseStatus, ['active', 'pending_renewal'], true) ? 'occupied' : 'available';
        $unit->save();
    }

    protected function markUnitAvailable(?int $unitId): void
    {
        if (! $unitId) {
            return;
        }

        $unit = PropertyUnit::query()->where('created_by', Auth::user()->creatorId())->find($unitId);
        if ($unit) {
            $unit->status = 'available';
            $unit->save();
        }
    }

    protected function owns(PropertyLease $propertyLease)
    {
        return (int) $propertyLease->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(PropertyLease $propertyLease)
    {
        return $this->owns($propertyLease) && (Auth::user()->can('manage property lease') || Auth::user()->can('show property lease'));
    }
}
