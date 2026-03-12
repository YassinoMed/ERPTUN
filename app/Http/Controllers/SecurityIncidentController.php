<?php

namespace App\Http\Controllers;

use App\Models\SecurityIncident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityIncidentController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage security incident') && ! Auth::user()->can('show security incident')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $incidents = SecurityIncident::where('created_by', Auth::user()->creatorId())->with(['reporter', 'owner'])->latest('id')->get();

        return view('security_incidents.index', compact('incidents'));
    }

    public function create()
    {
        if (! Auth::user()->can('create security incident')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('security_incidents.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create security incident')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'title' => 'required|max:255',
            'status' => 'required|max:50',
            'severity' => 'required|max:50',
        ]);

        SecurityIncident::create($request->only([
            'title', 'incident_type', 'severity', 'status', 'affected_module', 'reported_by', 'owner_id',
            'detected_at', 'summary', 'containment_actions', 'resolution_notes',
        ]) + [
            'incident_reference' => 'SEC-'.date('His'),
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('security-incidents.index')->with('success', __('Security incident successfully created.'));
    }

    public function show(SecurityIncident $securityIncident)
    {
        if (! $this->canAccess($securityIncident)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $securityIncident->load(['reporter', 'owner']);

        return view('security_incidents.show', compact('securityIncident'));
    }

    public function edit(SecurityIncident $securityIncident)
    {
        if (! Auth::user()->can('edit security incident') || ! $this->owns($securityIncident)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('security_incidents.edit', array_merge($this->formData(), compact('securityIncident')));
    }

    public function update(Request $request, SecurityIncident $securityIncident)
    {
        if (! Auth::user()->can('edit security incident') || ! $this->owns($securityIncident)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'title' => 'required|max:255',
            'status' => 'required|max:50',
            'severity' => 'required|max:50',
        ]);

        $securityIncident->update($request->only([
            'title', 'incident_type', 'severity', 'status', 'affected_module', 'reported_by', 'owner_id',
            'detected_at', 'summary', 'containment_actions', 'resolution_notes',
        ]));

        return redirect()->route('security-incidents.index')->with('success', __('Security incident successfully updated.'));
    }

    public function destroy(SecurityIncident $securityIncident)
    {
        if (! Auth::user()->can('delete security incident') || ! $this->owns($securityIncident)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $securityIncident->delete();

        return redirect()->route('security-incidents.index')->with('success', __('Security incident successfully deleted.'));
    }

    protected function formData(): array
    {
        $creatorId = Auth::user()->creatorId();

        return [
            'statuses' => SecurityIncident::$statuses,
            'severities' => SecurityIncident::$severities,
            'users' => User::where('created_by', $creatorId)->pluck('name', 'id'),
        ];
    }

    protected function owns(SecurityIncident $securityIncident): bool
    {
        return (int) $securityIncident->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(SecurityIncident $securityIncident): bool
    {
        return $this->owns($securityIncident) && (Auth::user()->can('manage security incident') || Auth::user()->can('show security incident'));
    }
}
