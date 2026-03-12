<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\HseIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HseIncidentController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage hse incident') && ! Auth::user()->can('show hse incident')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $hseIncidents = HseIncident::where('created_by', Auth::user()->creatorId())
            ->with('reporter')
            ->latest('id')
            ->get();

        return view('hse_incidents.index', compact('hseIncidents'));
    }

    public function create()
    {
        if (! Auth::user()->can('create hse incident')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('hse_incidents.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create hse incident')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'incident_code' => 'required|max:255',
            'title' => 'required|max:255',
            'category' => 'required|max:255',
            'severity' => 'required|max:100',
            'status' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        HseIncident::create($request->only([
            'incident_code', 'title', 'category', 'severity', 'status', 'occurred_on',
            'location', 'reported_by_employee_id', 'actions', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('hse-incidents.index')->with('success', __('HSE incident successfully created.'));
    }

    public function show(HseIncident $hseIncident)
    {
        if (! $this->canAccess($hseIncident)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $hseIncident->load('reporter');

        return view('hse_incidents.show', compact('hseIncident'));
    }

    public function edit(HseIncident $hseIncident)
    {
        if (! Auth::user()->can('edit hse incident') || ! $this->owns($hseIncident)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('hse_incidents.edit', $this->formData() + compact('hseIncident'));
    }

    public function update(Request $request, HseIncident $hseIncident)
    {
        if (! Auth::user()->can('edit hse incident') || ! $this->owns($hseIncident)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'incident_code' => 'required|max:255',
            'title' => 'required|max:255',
            'category' => 'required|max:255',
            'severity' => 'required|max:100',
            'status' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $hseIncident->update($request->only([
            'incident_code', 'title', 'category', 'severity', 'status', 'occurred_on',
            'location', 'reported_by_employee_id', 'actions', 'notes',
        ]));

        return redirect()->route('hse-incidents.show', $hseIncident)->with('success', __('HSE incident successfully updated.'));
    }

    public function destroy(HseIncident $hseIncident)
    {
        if (! Auth::user()->can('delete hse incident') || ! $this->owns($hseIncident)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $hseIncident->delete();

        return redirect()->route('hse-incidents.index')->with('success', __('HSE incident successfully deleted.'));
    }

    protected function formData(): array
    {
        return [
            'severities' => HseIncident::$severities,
            'statuses' => HseIncident::$statuses,
            'employees' => Employee::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
        ];
    }

    protected function owns(HseIncident $hseIncident): bool
    {
        return (int) $hseIncident->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(HseIncident $hseIncident): bool
    {
        return $this->owns($hseIncident) && (Auth::user()->can('manage hse incident') || Auth::user()->can('show hse incident'));
    }
}
