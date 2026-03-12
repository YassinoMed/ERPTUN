<?php

namespace App\Http\Controllers;

use App\Models\IndustrialCostRecord;
use App\Models\ProductService;
use App\Models\ProductionOrder;
use App\Models\User;
use Illuminate\Http\Request;

class IndustrialCostRecordController extends Controller
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
        if (!\Auth::user()->can('manage industrial cost record')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $costRecords = IndustrialCostRecord::where('created_by', \Auth::user()->creatorId())
            ->with(['order.product', 'product'])
            ->orderByDesc('id')
            ->get();

        return view('production.cost_records.index', compact('costRecords'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create industrial cost record')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $orders = ProductionOrder::where('created_by', \Auth::user()->creatorId())->orderByDesc('id')->get()->pluck('order_number', 'id');
        $products = ProductService::where('created_by', \Auth::user()->creatorId())->where('type', 'product')->orderBy('name')->get()->pluck('name', 'id');

        return view('production.cost_records.create', compact('orders', 'products'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create industrial cost record')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'production_order_id' => 'nullable|integer',
            'product_id' => 'nullable|integer',
            'cost_type' => 'required|in:material,machine,labor,overhead,subcontract',
            'amount' => 'required|numeric|min:0',
            'quantity_basis' => 'nullable|numeric|min:0',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        IndustrialCostRecord::create($request->only(['production_order_id', 'product_id', 'cost_type', 'amount', 'quantity_basis', 'notes']) + [
            'created_by' => \Auth::user()->creatorId(),
        ]);

        return redirect()->route('production.cost-records.index')->with('success', __('Cost record successfully created.'));
    }

    public function edit(IndustrialCostRecord $costRecord)
    {
        if (!\Auth::user()->can('edit industrial cost record')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        if ($costRecord->created_by != \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $orders = ProductionOrder::where('created_by', \Auth::user()->creatorId())->orderByDesc('id')->get()->pluck('order_number', 'id');
        $products = ProductService::where('created_by', \Auth::user()->creatorId())->where('type', 'product')->orderBy('name')->get()->pluck('name', 'id');

        return view('production.cost_records.edit', compact('costRecord', 'orders', 'products'));
    }

    public function update(Request $request, IndustrialCostRecord $costRecord)
    {
        if (!\Auth::user()->can('edit industrial cost record')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($costRecord->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'production_order_id' => 'nullable|integer',
            'product_id' => 'nullable|integer',
            'cost_type' => 'required|in:material,machine,labor,overhead,subcontract',
            'amount' => 'required|numeric|min:0',
            'quantity_basis' => 'nullable|numeric|min:0',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $costRecord->update($request->only(['production_order_id', 'product_id', 'cost_type', 'amount', 'quantity_basis', 'notes']));

        return redirect()->route('production.cost-records.index')->with('success', __('Cost record successfully updated.'));
    }

    public function destroy(IndustrialCostRecord $costRecord)
    {
        if (!\Auth::user()->can('delete industrial cost record')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($costRecord->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $costRecord->delete();

        return redirect()->route('production.cost-records.index')->with('success', __('Cost record successfully deleted.'));
    }
}
