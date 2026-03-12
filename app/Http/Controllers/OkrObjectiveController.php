<?php

namespace App\Http\Controllers;

use App\Models\OkrKeyResult;
use App\Models\OkrObjective;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OkrObjectiveController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage okr objective') && !Auth::user()->can('show okr objective')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $objectives = OkrObjective::where('created_by', Auth::user()->creatorId())
            ->withCount('keyResults')
            ->with(['owner', 'project'])
            ->latest('id')
            ->get();

        return view('okr_objectives.index', compact('objectives'));
    }

    public function create()
    {
        if (!Auth::user()->can('create okr objective')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $owners = $this->owners();
        $projects = $this->projects();
        $statuses = OkrObjective::$statuses;

        return view('okr_objectives.create', compact('owners', 'projects', 'statuses'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create okr objective')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'title' => 'required|max:255',
            'status' => 'required|max:50',
            'progress' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        OkrObjective::create($request->only([
            'title',
            'owner_id',
            'project_id',
            'cycle',
            'status',
            'progress',
            'start_date',
            'end_date',
            'description',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('okr-objectives.index')->with('success', __('OKR objective successfully created.'));
    }

    public function show(OkrObjective $okrObjective)
    {
        if (!$this->canAccess($okrObjective)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $okrObjective->load(['owner', 'project', 'keyResults']);
        $owners = $this->owners();
        $projects = $this->projects();
        $statuses = OkrObjective::$statuses;
        $keyResultStatuses = OkrKeyResult::$statuses;

        return view('okr_objectives.show', compact('okrObjective', 'owners', 'projects', 'statuses', 'keyResultStatuses'));
    }

    public function edit(OkrObjective $okrObjective)
    {
        if (!Auth::user()->can('edit okr objective') || !$this->owns($okrObjective)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $owners = $this->owners();
        $projects = $this->projects();
        $statuses = OkrObjective::$statuses;

        return view('okr_objectives.edit', compact('okrObjective', 'owners', 'projects', 'statuses'));
    }

    public function update(Request $request, OkrObjective $okrObjective)
    {
        if (!Auth::user()->can('edit okr objective') || !$this->owns($okrObjective)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'title' => 'required|max:255',
            'status' => 'required|max:50',
            'progress' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $okrObjective->update($request->only([
            'title',
            'owner_id',
            'project_id',
            'cycle',
            'status',
            'progress',
            'start_date',
            'end_date',
            'description',
        ]));

        return redirect()->route('okr-objectives.show', $okrObjective)->with('success', __('OKR objective successfully updated.'));
    }

    public function destroy(OkrObjective $okrObjective)
    {
        if (!Auth::user()->can('delete okr objective') || !$this->owns($okrObjective)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $okrObjective->delete();

        return redirect()->route('okr-objectives.index')->with('success', __('OKR objective successfully deleted.'));
    }

    public function storeKeyResult(Request $request, OkrObjective $okrObjective)
    {
        if (!Auth::user()->can('create okr key result') || !$this->owns($okrObjective)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'metric_name' => 'required|max:255',
            'target_value' => 'required|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'status' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        OkrKeyResult::create($request->only([
            'metric_name',
            'start_value',
            'target_value',
            'current_value',
            'unit',
            'status',
            'due_date',
        ]) + [
            'okr_objective_id' => $okrObjective->id,
            'created_by' => Auth::user()->creatorId(),
        ]);

        $this->syncProgress($okrObjective->fresh());

        return redirect()->route('okr-objectives.show', $okrObjective)->with('success', __('Key result successfully created.'));
    }

    public function editKeyResult(OkrKeyResult $okrKeyResult)
    {
        if (!Auth::user()->can('edit okr key result') || !$this->ownsKeyResult($okrKeyResult)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $keyResultStatuses = OkrKeyResult::$statuses;

        return view('okr_objectives.edit_key_result', compact('okrKeyResult', 'keyResultStatuses'));
    }

    public function updateKeyResult(Request $request, OkrKeyResult $okrKeyResult)
    {
        if (!Auth::user()->can('edit okr key result') || !$this->ownsKeyResult($okrKeyResult)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'metric_name' => 'required|max:255',
            'target_value' => 'required|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'status' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $okrKeyResult->update($request->only([
            'metric_name',
            'start_value',
            'target_value',
            'current_value',
            'unit',
            'status',
            'due_date',
        ]));

        $this->syncProgress($okrKeyResult->objective()->first());

        return redirect()->route('okr-objectives.show', $okrKeyResult->okr_objective_id)->with('success', __('Key result successfully updated.'));
    }

    public function destroyKeyResult(OkrKeyResult $okrKeyResult)
    {
        if (!Auth::user()->can('delete okr key result') || !$this->ownsKeyResult($okrKeyResult)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $objectiveId = $okrKeyResult->okr_objective_id;
        $okrKeyResult->delete();
        $objective = OkrObjective::find($objectiveId);
        if ($objective) {
            $this->syncProgress($objective);
        }

        return redirect()->route('okr-objectives.show', $objectiveId)->with('success', __('Key result successfully deleted.'));
    }

    protected function syncProgress(?OkrObjective $objective): void
    {
        if (!$objective) {
            return;
        }

        $keyResults = $objective->keyResults()->get();
        if ($keyResults->isEmpty()) {
            $objective->update(['progress' => 0]);
            return;
        }

        $progress = $keyResults->avg(function ($keyResult) {
            if ((float) $keyResult->target_value <= 0) {
                return 0;
            }

            return min(100, (((float) $keyResult->current_value) / ((float) $keyResult->target_value)) * 100);
        });

        $objective->update(['progress' => round($progress, 2)]);
    }

    protected function owners()
    {
        return User::where('created_by', Auth::user()->creatorId())
            ->whereNotIn('type', ['client', 'super admin'])
            ->pluck('name', 'id');
    }

    protected function projects()
    {
        return Project::where('created_by', Auth::user()->creatorId())->pluck('project_name', 'id');
    }

    protected function owns(OkrObjective $okrObjective)
    {
        return (int) $okrObjective->created_by === (int) Auth::user()->creatorId();
    }

    protected function ownsKeyResult(OkrKeyResult $okrKeyResult)
    {
        return (int) $okrKeyResult->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(OkrObjective $okrObjective)
    {
        return $this->owns($okrObjective) && (Auth::user()->can('manage okr objective') || Auth::user()->can('show okr objective'));
    }
}
