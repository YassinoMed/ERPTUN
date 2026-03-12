<?php

namespace App\Http\Controllers;

use App\Models\GdprProcessingActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GdprProcessingActivityController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage gdpr activity') && ! Auth::user()->can('show gdpr activity')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $activities = GdprProcessingActivity::where('created_by', Auth::user()->creatorId())->latest('id')->get();

        return view('gdpr_processing_activities.index', compact('activities'));
    }

    public function create()
    {
        if (! Auth::user()->can('create gdpr activity')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $statuses = GdprProcessingActivity::$statuses;

        return view('gdpr_processing_activities.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create gdpr activity')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'activity_name' => 'required|max:255',
            'status' => 'required|max:50',
        ]);

        GdprProcessingActivity::create($request->only([
            'activity_name', 'data_category', 'purpose', 'lawful_basis', 'processor_name', 'retention_period', 'status', 'notes',
        ]) + [
            'activity_code' => 'GDPR-'.date('His'),
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('gdpr-activities.index')->with('success', __('GDPR activity successfully created.'));
    }

    public function show(GdprProcessingActivity $gdprActivity)
    {
        if (! $this->canAccess($gdprActivity)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        return view('gdpr_processing_activities.show', compact('gdprActivity'));
    }

    public function edit(GdprProcessingActivity $gdprActivity)
    {
        if (! Auth::user()->can('edit gdpr activity') || ! $this->owns($gdprActivity)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $statuses = GdprProcessingActivity::$statuses;

        return view('gdpr_processing_activities.edit', compact('gdprActivity', 'statuses'));
    }

    public function update(Request $request, GdprProcessingActivity $gdprActivity)
    {
        if (! Auth::user()->can('edit gdpr activity') || ! $this->owns($gdprActivity)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'activity_name' => 'required|max:255',
            'status' => 'required|max:50',
        ]);

        $gdprActivity->update($request->only([
            'activity_name', 'data_category', 'purpose', 'lawful_basis', 'processor_name', 'retention_period', 'status', 'notes',
        ]));

        return redirect()->route('gdpr-activities.index')->with('success', __('GDPR activity successfully updated.'));
    }

    public function destroy(GdprProcessingActivity $gdprActivity)
    {
        if (! Auth::user()->can('delete gdpr activity') || ! $this->owns($gdprActivity)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $gdprActivity->delete();

        return redirect()->route('gdpr-activities.index')->with('success', __('GDPR activity successfully deleted.'));
    }

    protected function owns(GdprProcessingActivity $gdprActivity): bool
    {
        return (int) $gdprActivity->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(GdprProcessingActivity $gdprActivity): bool
    {
        return $this->owns($gdprActivity) && (Auth::user()->can('manage gdpr activity') || Auth::user()->can('show gdpr activity'));
    }
}
