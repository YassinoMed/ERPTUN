<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\InnovationIdea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InnovationIdeaController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage innovation idea') && !Auth::user()->can('show innovation idea')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ideas = InnovationIdea::where('created_by', Auth::user()->creatorId())->with('submitter')->latest('id')->get();

        return view('innovation_ideas.index', compact('ideas'));
    }

    public function create()
    {
        if (!Auth::user()->can('create innovation idea')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $employees = Employee::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $statuses = InnovationIdea::$statuses;
        $priorities = InnovationIdea::$priorities;

        return view('innovation_ideas.create', compact('employees', 'statuses', 'priorities'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create innovation idea')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'title' => 'required|max:255',
            'status' => 'required',
            'priority' => 'required',
            'expected_value' => 'nullable|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        InnovationIdea::create($request->only([
            'title',
            'category',
            'submitted_by',
            'status',
            'priority',
            'expected_value',
            'description',
            'business_case',
            'implementation_notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('innovation-ideas.index')->with('success', __('Innovation idea successfully created.'));
    }

    public function show(InnovationIdea $innovationIdea)
    {
        if (!$this->canAccess($innovationIdea)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $innovationIdea->load('submitter');

        return view('innovation_ideas.show', compact('innovationIdea'));
    }

    public function edit(InnovationIdea $innovationIdea)
    {
        if (!Auth::user()->can('edit innovation idea') || !$this->owns($innovationIdea)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $employees = Employee::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $statuses = InnovationIdea::$statuses;
        $priorities = InnovationIdea::$priorities;

        return view('innovation_ideas.edit', compact('innovationIdea', 'employees', 'statuses', 'priorities'));
    }

    public function update(Request $request, InnovationIdea $innovationIdea)
    {
        if (!Auth::user()->can('edit innovation idea') || !$this->owns($innovationIdea)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'title' => 'required|max:255',
            'status' => 'required',
            'priority' => 'required',
            'expected_value' => 'nullable|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $innovationIdea->update($request->only([
            'title',
            'category',
            'submitted_by',
            'status',
            'priority',
            'expected_value',
            'description',
            'business_case',
            'implementation_notes',
        ]));

        return redirect()->route('innovation-ideas.index')->with('success', __('Innovation idea successfully updated.'));
    }

    public function destroy(InnovationIdea $innovationIdea)
    {
        if (!Auth::user()->can('delete innovation idea') || !$this->owns($innovationIdea)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $innovationIdea->delete();

        return redirect()->route('innovation-ideas.index')->with('success', __('Innovation idea successfully deleted.'));
    }

    protected function owns(InnovationIdea $innovationIdea)
    {
        return (int) $innovationIdea->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(InnovationIdea $innovationIdea)
    {
        return $this->owns($innovationIdea) && (Auth::user()->can('manage innovation idea') || Auth::user()->can('show innovation idea'));
    }
}
