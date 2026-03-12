<?php

namespace App\Http\Controllers;

use App\Models\IndustrialSubcontractOrder;
use App\Models\ProductionOrder;
use App\Models\ProductionRoutingStep;
use App\Models\User;
use App\Models\Vender;
use Illuminate\Http\Request;

class IndustrialSubcontractOrderController extends Controller
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
        if (!\Auth::user()->can('manage industrial subcontract order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $subcontractOrders = IndustrialSubcontractOrder::where('created_by', \Auth::user()->creatorId())
            ->with(['order.product', 'step', 'vendor'])
            ->orderByDesc('id')
            ->get();

        return view('production.subcontract_orders.index', compact('subcontractOrders'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create industrial subcontract order')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $orders = ProductionOrder::where('created_by', \Auth::user()->creatorId())->orderByDesc('id')->get()->pluck('order_number', 'id');
        $steps = ProductionRoutingStep::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $vendors = Vender::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.subcontract_orders.create', compact('orders', 'steps', 'vendors'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create industrial subcontract order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'production_order_id' => 'nullable|integer',
            'production_routing_step_id' => 'nullable|integer',
            'vender_id' => 'nullable|integer',
            'reference' => 'nullable|max:255',
            'status' => 'required|in:draft,sent,in_progress,received,closed,cancelled',
            'quantity' => 'required|numeric|min:0.0001',
            'unit_cost' => 'nullable|numeric|min:0',
            'planned_send_date' => 'nullable|date',
            'planned_receive_date' => 'nullable|date',
            'actual_receive_date' => 'nullable|date',
            'quality_notes' => 'nullable',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        IndustrialSubcontractOrder::create($request->only([
            'production_order_id', 'production_routing_step_id', 'vender_id', 'reference', 'status', 'quantity', 'unit_cost',
            'planned_send_date', 'planned_receive_date', 'actual_receive_date', 'quality_notes', 'notes',
        ]) + ['created_by' => \Auth::user()->creatorId()]);

        return redirect()->route('production.subcontract-orders.index')->with('success', __('Subcontract order successfully created.'));
    }

    public function show(IndustrialSubcontractOrder $subcontractOrder)
    {
        if (!\Auth::user()->can('show industrial subcontract order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($subcontractOrder->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $subcontractOrder->load(['order.product', 'step.routing', 'vendor']);

        return view('production.subcontract_orders.show', compact('subcontractOrder'));
    }

    public function edit(IndustrialSubcontractOrder $subcontractOrder)
    {
        if (!\Auth::user()->can('edit industrial subcontract order')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        if ($subcontractOrder->created_by != \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $orders = ProductionOrder::where('created_by', \Auth::user()->creatorId())->orderByDesc('id')->get()->pluck('order_number', 'id');
        $steps = ProductionRoutingStep::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $vendors = Vender::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.subcontract_orders.edit', compact('subcontractOrder', 'orders', 'steps', 'vendors'));
    }

    public function update(Request $request, IndustrialSubcontractOrder $subcontractOrder)
    {
        if (!\Auth::user()->can('edit industrial subcontract order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($subcontractOrder->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'production_order_id' => 'nullable|integer',
            'production_routing_step_id' => 'nullable|integer',
            'vender_id' => 'nullable|integer',
            'reference' => 'nullable|max:255',
            'status' => 'required|in:draft,sent,in_progress,received,closed,cancelled',
            'quantity' => 'required|numeric|min:0.0001',
            'unit_cost' => 'nullable|numeric|min:0',
            'planned_send_date' => 'nullable|date',
            'planned_receive_date' => 'nullable|date',
            'actual_receive_date' => 'nullable|date',
            'quality_notes' => 'nullable',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $subcontractOrder->update($request->only([
            'production_order_id', 'production_routing_step_id', 'vender_id', 'reference', 'status', 'quantity', 'unit_cost',
            'planned_send_date', 'planned_receive_date', 'actual_receive_date', 'quality_notes', 'notes',
        ]));

        return redirect()->route('production.subcontract-orders.index')->with('success', __('Subcontract order successfully updated.'));
    }

    public function destroy(IndustrialSubcontractOrder $subcontractOrder)
    {
        if (!\Auth::user()->can('delete industrial subcontract order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($subcontractOrder->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $subcontractOrder->delete();

        return redirect()->route('production.subcontract-orders.index')->with('success', __('Subcontract order successfully deleted.'));
    }
}
