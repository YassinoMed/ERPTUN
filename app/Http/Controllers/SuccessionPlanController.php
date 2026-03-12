<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SuccessionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuccessionPlanController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage succession plan') && ! Auth::user()->can('show succession plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $successionPlans = SuccessionPlan::where('created_by', Auth::user()->creatorId())
            ->with(['employee', 'successor'])
            ->latest('id')
            ->get();

        return view('succession_plans.index', compact('successionPlans'));
    }

    public function create()
    {
        if (! Auth::user()->can('create succession plan')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('succession_plans.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create succession plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'readiness_level' => 'required|max:100',
            'risk_level' => 'required|max:100',
            'status' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        SuccessionPlan::create($request->only([
            'employee_id', 'successor_employee_id', 'readiness_level', 'risk_level',
            'target_date', 'status', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('succession-plans.index')->with('success', __('Succession plan successfully created.'));
    }

    public function show(SuccessionPlan $successionPlan)
    {
        if (! $this->canAccess($successionPlan)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $successionPlan->load(['employee', 'successor']);

        return view('succession_plans.show', compact('successionPlan'));
    }

    public function edit(SuccessionPlan $successionPlan)
    {
        if (! Auth::user()->can('edit succession plan') || ! $this->owns($successionPlan)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('succession_plans.edit', $this->formData() + compact('successionPlan'));
    }

    public function update(Request $request, SuccessionPlan $successionPlan)
    {
        if (! Auth::user()->can('edit succession plan') || ! $this->owns($successionPlan)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'readiness_level' => 'required|max:100',
            'risk_level' => 'required|max:100',
            'status' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $successionPlan->update($request->only([
            'employee_id', 'successor_employee_id', 'readiness_level', 'risk_level',
            'target_date', 'status', 'notes',
        ]));

        return redirect()->route('succession-plans.show', $successionPlan)->with('success', __('Succession plan successfully updated.'));
    }

    public function destroy(SuccessionPlan $successionPlan)
    {
        if (! Auth::user()->can('delete succession plan') || ! $this->owns($successionPlan)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $successionPlan->delete();

        return redirect()->route('succession-plans.index')->with('success', __('Succession plan successfully deleted.'));
    }

    protected function formData(): array
    {
        return [
            'levels' => SuccessionPlan::$levels,
            'statuses' => SuccessionPlan::$statuses,
            'employees' => Employee::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
        ];
    }

    protected function owns(SuccessionPlan $successionPlan): bool
    {
        return (int) $successionPlan->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(SuccessionPlan $successionPlan): bool
    {
        return $this->owns($successionPlan) && (Auth::user()->can('manage succession plan') || Auth::user()->can('show succession plan'));
    }
}
