<?php

namespace App\Http\Controllers;

use App\Models\InsurancePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsurancePolicyController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage insurance policy') && ! Auth::user()->can('show insurance policy')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $policies = InsurancePolicy::query()
            ->where('created_by', Auth::user()->creatorId())
            ->withCount('claims')
            ->latest('id')
            ->get();

        return view('insurance_policies.index', compact('policies'));
    }

    public function create()
    {
        if (! Auth::user()->can('create insurance policy')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $statuses = InsurancePolicy::$statuses;

        return view('insurance_policies.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create insurance policy')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'policy_number' => 'required|max:255',
            'provider_name' => 'required|max:255',
            'policy_name' => 'required|max:255',
            'status' => 'required',
            'premium_amount' => 'nullable|numeric|min:0',
            'coverage_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        InsurancePolicy::create($request->only([
            'policy_number',
            'provider_name',
            'policy_name',
            'coverage_type',
            'insured_party',
            'insured_asset',
            'start_date',
            'end_date',
            'premium_amount',
            'coverage_amount',
            'status',
            'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('insurance-policies.index')->with('success', __('Insurance policy successfully created.'));
    }

    public function show(InsurancePolicy $insurancePolicy)
    {
        if (! $this->canAccess($insurancePolicy)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $insurancePolicy->load('claims.assignee', 'claims.customer');

        return view('insurance_policies.show', compact('insurancePolicy'));
    }

    public function edit(InsurancePolicy $insurancePolicy)
    {
        if (! Auth::user()->can('edit insurance policy') || ! $this->owns($insurancePolicy)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $statuses = InsurancePolicy::$statuses;

        return view('insurance_policies.edit', compact('insurancePolicy', 'statuses'));
    }

    public function update(Request $request, InsurancePolicy $insurancePolicy)
    {
        if (! Auth::user()->can('edit insurance policy') || ! $this->owns($insurancePolicy)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'policy_number' => 'required|max:255',
            'provider_name' => 'required|max:255',
            'policy_name' => 'required|max:255',
            'status' => 'required',
            'premium_amount' => 'nullable|numeric|min:0',
            'coverage_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $insurancePolicy->update($request->only([
            'policy_number',
            'provider_name',
            'policy_name',
            'coverage_type',
            'insured_party',
            'insured_asset',
            'start_date',
            'end_date',
            'premium_amount',
            'coverage_amount',
            'status',
            'notes',
        ]));

        return redirect()->route('insurance-policies.index')->with('success', __('Insurance policy successfully updated.'));
    }

    public function destroy(InsurancePolicy $insurancePolicy)
    {
        if (! Auth::user()->can('delete insurance policy') || ! $this->owns($insurancePolicy)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $insurancePolicy->delete();

        return redirect()->route('insurance-policies.index')->with('success', __('Insurance policy successfully deleted.'));
    }

    protected function owns(InsurancePolicy $insurancePolicy)
    {
        return (int) $insurancePolicy->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(InsurancePolicy $insurancePolicy)
    {
        return $this->owns($insurancePolicy) && (Auth::user()->can('manage insurance policy') || Auth::user()->can('show insurance policy'));
    }
}
