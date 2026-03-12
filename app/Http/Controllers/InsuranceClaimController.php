<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\InsuranceClaim;
use App\Models\InsurancePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsuranceClaimController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage insurance claim') && ! Auth::user()->can('show insurance claim')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $claims = InsuranceClaim::query()
            ->where('created_by', Auth::user()->creatorId())
            ->with(['policy', 'customer', 'assignee'])
            ->latest('id')
            ->get();

        return view('insurance_claims.index', compact('claims'));
    }

    public function create()
    {
        if (! Auth::user()->can('create insurance claim')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $policies = InsurancePolicy::query()->where('created_by', Auth::user()->creatorId())->pluck('policy_name', 'id');
        $customers = Customer::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $employees = Employee::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $priorities = InsuranceClaim::$priorities;
        $statuses = InsuranceClaim::$statuses;

        return view('insurance_claims.create', compact('policies', 'customers', 'employees', 'priorities', 'statuses'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create insurance claim')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'insurance_policy_id' => 'required|integer|exists:insurance_policies,id',
            'claim_number' => 'required|max:255',
            'status' => 'required',
            'priority' => 'required',
            'amount_claimed' => 'nullable|numeric|min:0',
            'amount_settled' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        InsuranceClaim::create($request->only([
            'insurance_policy_id',
            'customer_id',
            'assigned_to',
            'claim_number',
            'incident_date',
            'reported_date',
            'amount_claimed',
            'amount_settled',
            'priority',
            'status',
            'incident_type',
            'location',
            'description',
            'resolution_notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('insurance-claims.index')->with('success', __('Insurance claim successfully created.'));
    }

    public function show(InsuranceClaim $insuranceClaim)
    {
        if (! $this->canAccess($insuranceClaim)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $insuranceClaim->load(['policy', 'customer', 'assignee']);

        return view('insurance_claims.show', compact('insuranceClaim'));
    }

    public function edit(InsuranceClaim $insuranceClaim)
    {
        if (! Auth::user()->can('edit insurance claim') || ! $this->owns($insuranceClaim)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $policies = InsurancePolicy::query()->where('created_by', Auth::user()->creatorId())->pluck('policy_name', 'id');
        $customers = Customer::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $employees = Employee::query()->where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $priorities = InsuranceClaim::$priorities;
        $statuses = InsuranceClaim::$statuses;

        return view('insurance_claims.edit', compact('insuranceClaim', 'policies', 'customers', 'employees', 'priorities', 'statuses'));
    }

    public function update(Request $request, InsuranceClaim $insuranceClaim)
    {
        if (! Auth::user()->can('edit insurance claim') || ! $this->owns($insuranceClaim)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'insurance_policy_id' => 'required|integer|exists:insurance_policies,id',
            'claim_number' => 'required|max:255',
            'status' => 'required',
            'priority' => 'required',
            'amount_claimed' => 'nullable|numeric|min:0',
            'amount_settled' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $insuranceClaim->update($request->only([
            'insurance_policy_id',
            'customer_id',
            'assigned_to',
            'claim_number',
            'incident_date',
            'reported_date',
            'amount_claimed',
            'amount_settled',
            'priority',
            'status',
            'incident_type',
            'location',
            'description',
            'resolution_notes',
        ]));

        return redirect()->route('insurance-claims.index')->with('success', __('Insurance claim successfully updated.'));
    }

    public function destroy(InsuranceClaim $insuranceClaim)
    {
        if (! Auth::user()->can('delete insurance claim') || ! $this->owns($insuranceClaim)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $insuranceClaim->delete();

        return redirect()->route('insurance-claims.index')->with('success', __('Insurance claim successfully deleted.'));
    }

    protected function owns(InsuranceClaim $insuranceClaim)
    {
        return (int) $insuranceClaim->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(InsuranceClaim $insuranceClaim)
    {
        return $this->owns($insuranceClaim) && (Auth::user()->can('manage insurance claim') || Auth::user()->can('show insurance claim'));
    }
}
