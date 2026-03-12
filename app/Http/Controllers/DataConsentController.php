<?php

namespace App\Http\Controllers;

use App\Models\DataConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataConsentController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage data consent') && ! Auth::user()->can('show data consent')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $consents = DataConsent::where('created_by', Auth::user()->creatorId())->latest('id')->get();

        return view('data_consents.index', compact('consents'));
    }

    public function create()
    {
        if (! Auth::user()->can('create data consent')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $statuses = DataConsent::$statuses;

        return view('data_consents.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create data consent')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'subject_name' => 'required|max:255',
            'status' => 'required|max:50',
        ]);

        DataConsent::create($request->only([
            'subject_type', 'subject_name', 'subject_reference', 'purpose', 'channel', 'status', 'consented_at',
            'expires_at', 'evidence_reference', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('data-consents.index')->with('success', __('Data consent successfully created.'));
    }

    public function show(DataConsent $dataConsent)
    {
        if (! $this->canAccess($dataConsent)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        return view('data_consents.show', compact('dataConsent'));
    }

    public function edit(DataConsent $dataConsent)
    {
        if (! Auth::user()->can('edit data consent') || ! $this->owns($dataConsent)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $statuses = DataConsent::$statuses;

        return view('data_consents.edit', compact('dataConsent', 'statuses'));
    }

    public function update(Request $request, DataConsent $dataConsent)
    {
        if (! Auth::user()->can('edit data consent') || ! $this->owns($dataConsent)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'subject_name' => 'required|max:255',
            'status' => 'required|max:50',
        ]);

        $dataConsent->update($request->only([
            'subject_type', 'subject_name', 'subject_reference', 'purpose', 'channel', 'status', 'consented_at',
            'expires_at', 'evidence_reference', 'notes',
        ]));

        return redirect()->route('data-consents.index')->with('success', __('Data consent successfully updated.'));
    }

    public function destroy(DataConsent $dataConsent)
    {
        if (! Auth::user()->can('delete data consent') || ! $this->owns($dataConsent)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $dataConsent->delete();

        return redirect()->route('data-consents.index')->with('success', __('Data consent successfully deleted.'));
    }

    protected function owns(DataConsent $dataConsent): bool
    {
        return (int) $dataConsent->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(DataConsent $dataConsent): bool
    {
        return $this->owns($dataConsent) && (Auth::user()->can('manage data consent') || Auth::user()->can('show data consent'));
    }
}
