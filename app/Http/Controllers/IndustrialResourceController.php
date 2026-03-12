<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\IndustrialResource;
use App\Models\ProductionWorkCenter;
use App\Models\User;
use Illuminate\Http\Request;

class IndustrialResourceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (\Auth::check() && \Auth::user()->type !== 'super admin' && (int) User::show_production() !== 1) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => __('Permission denied.')], 403);
                }

                return redirect()->route('dashboard')->with('error', __('Permission denied.'));
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if (!\Auth::user()->can('manage industrial resource')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $query = IndustrialResource::where('created_by', \Auth::user()->creatorId())
            ->with(['parent', 'branch', 'manager'])
            ->orderBy('type')
            ->orderBy('name');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $resources = $query->get();

        return view('production.industrial_resources.index', compact('resources'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create industrial resource')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $parentResources = IndustrialResource::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $branches = Branch::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $managers = Employee::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.industrial_resources.create', compact('parentResources', 'branches', 'managers'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create industrial resource')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'type' => 'required|in:site,workshop,line,station',
            'parent_id' => 'nullable|integer',
            'branch_id' => 'nullable|integer',
            'manager_id' => 'nullable|integer',
            'code' => 'nullable|max:255',
            'name' => 'required|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'capacity_hours_per_day' => 'nullable|numeric|min:0',
            'capacity_workers' => 'nullable|integer|min:0',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        IndustrialResource::create($request->only([
            'type', 'parent_id', 'branch_id', 'manager_id', 'code', 'name', 'status', 'capacity_hours_per_day', 'capacity_workers', 'notes',
        ]) + ['created_by' => \Auth::user()->creatorId()]);

        return redirect()->route('production.resources.index')->with('success', __('Industrial resource successfully created.'));
    }

    public function show(IndustrialResource $resource)
    {
        if (!\Auth::user()->can('show industrial resource')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($resource->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $resource->load(['parent', 'children', 'branch', 'manager', 'workCenters']);

        return view('production.industrial_resources.show', compact('resource'));
    }

    public function edit(IndustrialResource $resource)
    {
        if (!\Auth::user()->can('edit industrial resource')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        if ($resource->created_by != \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $parentResources = IndustrialResource::where('created_by', \Auth::user()->creatorId())
            ->where('id', '!=', $resource->id)
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');
        $branches = Branch::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $managers = Employee::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.industrial_resources.edit', compact('resource', 'parentResources', 'branches', 'managers'));
    }

    public function update(Request $request, IndustrialResource $resource)
    {
        if (!\Auth::user()->can('edit industrial resource')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($resource->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'type' => 'required|in:site,workshop,line,station',
            'parent_id' => 'nullable|integer|not_in:' . $resource->id,
            'branch_id' => 'nullable|integer',
            'manager_id' => 'nullable|integer',
            'code' => 'nullable|max:255',
            'name' => 'required|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'capacity_hours_per_day' => 'nullable|numeric|min:0',
            'capacity_workers' => 'nullable|integer|min:0',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $resource->update($request->only([
            'type', 'parent_id', 'branch_id', 'manager_id', 'code', 'name', 'status', 'capacity_hours_per_day', 'capacity_workers', 'notes',
        ]));

        return redirect()->route('production.resources.index')->with('success', __('Industrial resource successfully updated.'));
    }

    public function destroy(IndustrialResource $resource)
    {
        if (!\Auth::user()->can('delete industrial resource')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($resource->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($resource->children()->exists() || ProductionWorkCenter::where('industrial_resource_id', $resource->id)->exists()) {
            return redirect()->back()->with('error', __('This resource is linked to child resources or work centers.'));
        }

        $resource->delete();

        return redirect()->route('production.resources.index')->with('success', __('Industrial resource successfully deleted.'));
    }
}
