<?php

namespace App\Http\Controllers;

use App\Models\IndustrialResource;
use App\Models\ProductService;
use App\Models\ProductionRouting;
use App\Models\ProductionRoutingStep;
use App\Models\ProductionWorkCenter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionRoutingController extends Controller
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
        if (!\Auth::user()->can('manage production routing')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $routings = ProductionRouting::where('created_by', \Auth::user()->creatorId())
            ->with(['product', 'steps'])
            ->orderByDesc('id')
            ->get();

        return view('production.routings.index', compact('routings'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create production routing')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $products = ProductService::where('created_by', \Auth::user()->creatorId())->where('type', 'product')->orderBy('name')->get()->pluck('name', 'id');
        $workCenters = ProductionWorkCenter::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $resources = IndustrialResource::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');

        return view('production.routings.create', compact('products', 'workCenters', 'resources'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create production routing')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'product_id' => 'nullable|integer',
            'code' => 'nullable|max:255',
            'name' => 'required|max:255',
            'status' => 'required|in:active,draft,archived',
            'notes' => 'nullable',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|max:255',
            'steps.*.sequence' => 'nullable|integer|min:1',
            'steps.*.work_center_id' => 'nullable|integer',
            'steps.*.industrial_resource_id' => 'nullable|integer',
            'steps.*.planned_minutes' => 'nullable|integer|min:0',
            'steps.*.setup_cost' => 'nullable|numeric|min:0',
            'steps.*.run_cost' => 'nullable|numeric|min:0',
            'steps.*.scrap_percent' => 'nullable|numeric|min:0',
            'steps.*.instructions' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first())->withInput();
        }

        DB::transaction(function () use ($request) {
            $routing = ProductionRouting::create([
                'product_id' => $request->product_id,
                'code' => $request->code,
                'name' => $request->name,
                'status' => $request->status,
                'notes' => $request->notes,
                'created_by' => \Auth::user()->creatorId(),
            ]);

            foreach ($request->steps as $index => $step) {
                if (empty($step['name'])) {
                    continue;
                }

                ProductionRoutingStep::create([
                    'production_routing_id' => $routing->id,
                    'sequence' => $step['sequence'] ?? ($index + 1),
                    'name' => $step['name'],
                    'work_center_id' => $step['work_center_id'] ?? null,
                    'industrial_resource_id' => $step['industrial_resource_id'] ?? null,
                    'planned_minutes' => $step['planned_minutes'] ?? 0,
                    'setup_cost' => $step['setup_cost'] ?? 0,
                    'run_cost' => $step['run_cost'] ?? 0,
                    'scrap_percent' => $step['scrap_percent'] ?? 0,
                    'is_subcontracted' => !empty($step['is_subcontracted']),
                    'instructions' => $step['instructions'] ?? null,
                    'created_by' => \Auth::user()->creatorId(),
                ]);
            }
        });

        return redirect()->route('production.routings.index')->with('success', __('Production routing successfully created.'));
    }

    public function show(ProductionRouting $routing)
    {
        if (!\Auth::user()->can('show production routing')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($routing->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $routing->load(['product', 'steps.workCenter', 'steps.resource', 'orders']);

        return view('production.routings.show', compact('routing'));
    }

    public function edit(ProductionRouting $routing)
    {
        if (!\Auth::user()->can('edit production routing')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        if ($routing->created_by != \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $products = ProductService::where('created_by', \Auth::user()->creatorId())->where('type', 'product')->orderBy('name')->get()->pluck('name', 'id');
        $workCenters = ProductionWorkCenter::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $resources = IndustrialResource::where('created_by', \Auth::user()->creatorId())->orderBy('name')->get()->pluck('name', 'id');
        $routing->load('steps');

        return view('production.routings.edit', compact('routing', 'products', 'workCenters', 'resources'));
    }

    public function update(Request $request, ProductionRouting $routing)
    {
        if (!\Auth::user()->can('edit production routing')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($routing->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'product_id' => 'nullable|integer',
            'code' => 'nullable|max:255',
            'name' => 'required|max:255',
            'status' => 'required|in:active,draft,archived',
            'notes' => 'nullable',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|max:255',
            'steps.*.sequence' => 'nullable|integer|min:1',
            'steps.*.work_center_id' => 'nullable|integer',
            'steps.*.industrial_resource_id' => 'nullable|integer',
            'steps.*.planned_minutes' => 'nullable|integer|min:0',
            'steps.*.setup_cost' => 'nullable|numeric|min:0',
            'steps.*.run_cost' => 'nullable|numeric|min:0',
            'steps.*.scrap_percent' => 'nullable|numeric|min:0',
            'steps.*.instructions' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first())->withInput();
        }

        DB::transaction(function () use ($request, $routing) {
            $routing->update([
                'product_id' => $request->product_id,
                'code' => $request->code,
                'name' => $request->name,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            ProductionRoutingStep::where('production_routing_id', $routing->id)->delete();

            foreach ($request->steps as $index => $step) {
                if (empty($step['name'])) {
                    continue;
                }

                ProductionRoutingStep::create([
                    'production_routing_id' => $routing->id,
                    'sequence' => $step['sequence'] ?? ($index + 1),
                    'name' => $step['name'],
                    'work_center_id' => $step['work_center_id'] ?? null,
                    'industrial_resource_id' => $step['industrial_resource_id'] ?? null,
                    'planned_minutes' => $step['planned_minutes'] ?? 0,
                    'setup_cost' => $step['setup_cost'] ?? 0,
                    'run_cost' => $step['run_cost'] ?? 0,
                    'scrap_percent' => $step['scrap_percent'] ?? 0,
                    'is_subcontracted' => !empty($step['is_subcontracted']),
                    'instructions' => $step['instructions'] ?? null,
                    'created_by' => \Auth::user()->creatorId(),
                ]);
            }
        });

        return redirect()->route('production.routings.index')->with('success', __('Production routing successfully updated.'));
    }

    public function destroy(ProductionRouting $routing)
    {
        if (!\Auth::user()->can('delete production routing')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($routing->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        ProductionRoutingStep::where('production_routing_id', $routing->id)->delete();
        $routing->delete();

        return redirect()->route('production.routings.index')->with('success', __('Production routing successfully deleted.'));
    }
}
