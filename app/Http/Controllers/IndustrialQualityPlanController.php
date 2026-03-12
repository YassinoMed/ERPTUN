<?php

namespace App\Http\Controllers;

use App\Models\IndustrialQualityPlan;
use App\Models\ProductService;
use App\Models\ProductionRouting;
use App\Models\User;
use Illuminate\Http\Request;

class IndustrialQualityPlanController extends Controller
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
        if (!\Auth::user()->can('manage industrial quality plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $qualityPlans = IndustrialQualityPlan::where('created_by', \Auth::user()->creatorId())
            ->with(['product', 'routing'])
            ->orderByDesc('id')
            ->get();

        return view('production.quality_plans.index', compact('qualityPlans'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create industrial quality plan')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $products = ProductService::where('created_by', \Auth::user()->creatorId())->where('type', 'product')->orderBy('name')->get()->pluck('name', 'id');
        $routings = ProductionRouting::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.quality_plans.create', compact('products', 'routings'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create industrial quality plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'product_id' => 'nullable|integer',
            'production_routing_id' => 'nullable|integer',
            'name' => 'required|max:255',
            'check_stage' => 'required|in:incoming,in_process,final',
            'sampling_rule' => 'nullable|max:255',
            'status' => 'required|in:active,inactive',
            'acceptance_criteria' => 'nullable',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        IndustrialQualityPlan::create($request->only([
            'product_id', 'production_routing_id', 'name', 'check_stage', 'sampling_rule', 'status', 'acceptance_criteria', 'notes',
        ]) + ['created_by' => \Auth::user()->creatorId()]);

        return redirect()->route('production.quality-plans.index')->with('success', __('Quality plan successfully created.'));
    }

    public function edit(IndustrialQualityPlan $qualityPlan)
    {
        if (!\Auth::user()->can('edit industrial quality plan')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        if ($qualityPlan->created_by != \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $products = ProductService::where('created_by', \Auth::user()->creatorId())->where('type', 'product')->orderBy('name')->get()->pluck('name', 'id');
        $routings = ProductionRouting::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.quality_plans.edit', compact('qualityPlan', 'products', 'routings'));
    }

    public function update(Request $request, IndustrialQualityPlan $qualityPlan)
    {
        if (!\Auth::user()->can('edit industrial quality plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($qualityPlan->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'product_id' => 'nullable|integer',
            'production_routing_id' => 'nullable|integer',
            'name' => 'required|max:255',
            'check_stage' => 'required|in:incoming,in_process,final',
            'sampling_rule' => 'nullable|max:255',
            'status' => 'required|in:active,inactive',
            'acceptance_criteria' => 'nullable',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $qualityPlan->update($request->only([
            'product_id', 'production_routing_id', 'name', 'check_stage', 'sampling_rule', 'status', 'acceptance_criteria', 'notes',
        ]));

        return redirect()->route('production.quality-plans.index')->with('success', __('Quality plan successfully updated.'));
    }

    public function destroy(IndustrialQualityPlan $qualityPlan)
    {
        if (!\Auth::user()->can('delete industrial quality plan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($qualityPlan->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $qualityPlan->delete();

        return redirect()->route('production.quality-plans.index')->with('success', __('Quality plan successfully deleted.'));
    }
}
