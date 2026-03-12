<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\IndustrialMaintenanceOrder;
use App\Models\IndustrialResource;
use App\Models\ProductionWorkCenter;
use App\Models\User;
use Illuminate\Http\Request;

class IndustrialMaintenanceOrderController extends Controller
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

    public function index()
    {
        if (!\Auth::user()->can('manage industrial maintenance order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $maintenanceOrders = IndustrialMaintenanceOrder::where('created_by', \Auth::user()->creatorId())
            ->with(['workCenter', 'resource', 'assignee'])
            ->orderByDesc('id')
            ->get();

        return view('production.maintenance_orders.index', compact('maintenanceOrders'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create industrial maintenance order')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $workCenters = ProductionWorkCenter::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $resources = IndustrialResource::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $employees = Employee::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.maintenance_orders.create', compact('workCenters', 'resources', 'employees'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create industrial maintenance order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'work_center_id' => 'nullable|integer',
            'industrial_resource_id' => 'nullable|integer',
            'assigned_to' => 'nullable|integer',
            'reference' => 'nullable|max:255',
            'type' => 'required|in:preventive,corrective,predictive',
            'status' => 'required|in:open,in_progress,completed,cancelled',
            'planned_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'downtime_minutes' => 'nullable|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'checklist' => 'nullable',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        IndustrialMaintenanceOrder::create($request->only([
            'work_center_id', 'industrial_resource_id', 'assigned_to', 'reference', 'type', 'status', 'planned_date',
            'completed_date', 'downtime_minutes', 'cost', 'checklist', 'notes',
        ]) + ['created_by' => \Auth::user()->creatorId()]);

        return redirect()->route('production.maintenance-orders.index')->with('success', __('Maintenance order successfully created.'));
    }

    public function edit(IndustrialMaintenanceOrder $maintenanceOrder)
    {
        if (!\Auth::user()->can('edit industrial maintenance order')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        if ($maintenanceOrder->created_by != \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $workCenters = ProductionWorkCenter::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $resources = IndustrialResource::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $employees = Employee::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.maintenance_orders.edit', compact('maintenanceOrder', 'workCenters', 'resources', 'employees'));
    }

    public function update(Request $request, IndustrialMaintenanceOrder $maintenanceOrder)
    {
        if (!\Auth::user()->can('edit industrial maintenance order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($maintenanceOrder->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'work_center_id' => 'nullable|integer',
            'industrial_resource_id' => 'nullable|integer',
            'assigned_to' => 'nullable|integer',
            'reference' => 'nullable|max:255',
            'type' => 'required|in:preventive,corrective,predictive',
            'status' => 'required|in:open,in_progress,completed,cancelled',
            'planned_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'downtime_minutes' => 'nullable|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'checklist' => 'nullable',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $maintenanceOrder->update($request->only([
            'work_center_id', 'industrial_resource_id', 'assigned_to', 'reference', 'type', 'status', 'planned_date',
            'completed_date', 'downtime_minutes', 'cost', 'checklist', 'notes',
        ]));

        return redirect()->route('production.maintenance-orders.index')->with('success', __('Maintenance order successfully updated.'));
    }

    public function destroy(IndustrialMaintenanceOrder $maintenanceOrder)
    {
        if (!\Auth::user()->can('delete industrial maintenance order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($maintenanceOrder->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $maintenanceOrder->delete();

        return redirect()->route('production.maintenance-orders.index')->with('success', __('Maintenance order successfully deleted.'));
    }
}
