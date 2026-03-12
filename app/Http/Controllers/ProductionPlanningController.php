<?php

namespace App\Http\Controllers;

use App\Models\IndustrialCostRecord;
use App\Models\IndustrialMaintenanceOrder;
use App\Models\IndustrialSubcontractOrder;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderOperation;
use App\Models\ProductionQualityCheck;
use App\Models\ProductionShiftTeam;
use App\Models\ProductionShopfloorEvent;
use App\Models\ProductionTimeLog;
use App\Models\ProductionWorkCenter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionPlanningController extends Controller
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
        if (!\Auth::user()->can('show industrial planning')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creatorId = \Auth::user()->creatorId();

        $workCenters = ProductionWorkCenter::where('created_by', $creatorId)
            ->withCount(['productionOrders as active_orders_count' => function ($query) {
                $query->whereIn('status', ['draft', 'planned', 'in_progress']);
            }])
            ->orderBy('name')
            ->get();

        $orders = ProductionOrder::where('created_by', $creatorId)
            ->with(['product', 'workCenter.resource', 'shiftTeam', 'routing'])
            ->whereIn('status', ['draft', 'planned', 'in_progress'])
            ->orderBy('planned_start_date')
            ->get();

        $subcontractSummary = IndustrialSubcontractOrder::where('created_by', $creatorId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $maintenanceSummary = IndustrialMaintenanceOrder::where('created_by', $creatorId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $costSummary = IndustrialCostRecord::where('created_by', $creatorId)
            ->select('cost_type', DB::raw('sum(amount) as total'))
            ->groupBy('cost_type')
            ->pluck('total', 'cost_type');

        $shiftTeams = ProductionShiftTeam::where('created_by', $creatorId)->orderBy('name')->get();

        $plannedMachineByCenter = ProductionOrder::where('created_by', $creatorId)
            ->whereIn('status', ['draft', 'planned', 'in_progress'])
            ->select('work_center_id', DB::raw('sum(planned_machine_hours) as total_machine_hours'))
            ->groupBy('work_center_id')
            ->pluck('total_machine_hours', 'work_center_id');

        $plannedLaborByCenter = ProductionOrder::where('created_by', $creatorId)
            ->whereIn('status', ['draft', 'planned', 'in_progress'])
            ->select('work_center_id', DB::raw('sum(planned_labor_hours) as total_labor_hours'))
            ->groupBy('work_center_id')
            ->pluck('total_labor_hours', 'work_center_id');

        $actualMinutesByCenter = ProductionTimeLog::where('created_by', $creatorId)
            ->select('work_center_id', DB::raw('sum(minutes) as total_minutes'))
            ->groupBy('work_center_id')
            ->pluck('total_minutes', 'work_center_id');

        $downtimeByCenter = ProductionShopfloorEvent::where('created_by', $creatorId)
            ->where('event_type', 'downtime')
            ->select('production_work_center_id', DB::raw('sum(downtime_minutes) as total_downtime'))
            ->groupBy('production_work_center_id')
            ->pluck('total_downtime', 'production_work_center_id');

        $machineLoadSummary = $workCenters->map(function ($workCenter) use ($plannedMachineByCenter, $actualMinutesByCenter, $downtimeByCenter) {
            $planned = (float) ($plannedMachineByCenter[$workCenter->id] ?? 0);
            $actualHours = round(((float) ($actualMinutesByCenter[$workCenter->id] ?? 0)) / 60, 2);
            $available = max((float) $workCenter->capacity_hours_per_day, 1);
            $utilization = round(($planned / $available) * 100, 1);
            $loadGap = round($planned - $available, 2);
            $downtime = (int) ($downtimeByCenter[$workCenter->id] ?? 0);
            $downtimeRate = round(($downtime / max(($available * 60), 1)) * 100, 1);

            return [
                'name' => $workCenter->name,
                'planned_hours' => $planned,
                'actual_hours' => $actualHours,
                'available_hours' => $available,
                'utilization_percent' => $utilization,
                'load_gap_hours' => $loadGap,
                'saturation_status' => $utilization >= 100 ? 'overloaded' : ($utilization >= 80 ? 'tight' : 'balanced'),
                'downtime_minutes' => $downtime,
                'downtime_rate' => $downtimeRate,
            ];
        })->sortByDesc('utilization_percent')->values();

        $laborMinutesByCenter = ProductionTimeLog::where('created_by', $creatorId)
            ->select('work_center_id', DB::raw('sum(minutes) as total_minutes'))
            ->groupBy('work_center_id')
            ->pluck('total_minutes', 'work_center_id');

        $laborLoadSummary = $workCenters->map(function ($workCenter) use ($plannedLaborByCenter, $laborMinutesByCenter) {
            $plannedHours = (float) ($plannedLaborByCenter[$workCenter->id] ?? 0);
            $workers = max((int) $workCenter->capacity_workers, 1);
            $hoursPerWorker = round($plannedHours / $workers, 2);
            $actualHours = round(((float) ($laborMinutesByCenter[$workCenter->id] ?? 0)) / 60, 2);
            $gapHours = round($plannedHours - $actualHours, 2);
            $utilizationPercent = round(($plannedHours / max(($workers * 8), 1)) * 100, 1);

            return [
                'name' => $workCenter->name,
                'planned_hours' => $plannedHours,
                'actual_hours' => $actualHours,
                'gap_hours' => $gapHours,
                'workers' => $workers,
                'hours_per_worker' => $hoursPerWorker,
                'utilization_percent' => $utilizationPercent,
            ];
        })->sortByDesc('planned_hours')->values();

        $shopfloorEvents = ProductionShopfloorEvent::where('created_by', $creatorId)
            ->with(['order.product', 'workCenter', 'employee'])
            ->latest('happened_at')
            ->limit(12)
            ->get();

        $shopfloorSummary = ProductionShopfloorEvent::where('created_by', $creatorId)
            ->where('happened_at', '>=', Carbon::now()->subDay())
            ->select('event_type', DB::raw('count(*) as total'))
            ->groupBy('event_type')
            ->pluck('total', 'event_type');

        return view('production.planning.index', compact(
            'workCenters',
            'orders',
            'subcontractSummary',
            'maintenanceSummary',
            'costSummary',
            'shiftTeams',
            'machineLoadSummary',
            'laborLoadSummary',
            'shopfloorEvents',
            'shopfloorSummary'
        ));
    }

    public function storeShopfloorEvent(Request $request)
    {
        if (!\Auth::user()->can('show industrial planning')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'production_order_id' => 'nullable|integer',
            'production_work_center_id' => 'required|integer',
            'employee_id' => 'nullable|integer',
            'event_type' => 'required|string|in:status,downtime,output,quality_hold',
            'status' => 'required|string|max:64',
            'quantity' => 'nullable|numeric|min:0',
            'downtime_minutes' => 'nullable|integer|min:0',
            'happened_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        ProductionWorkCenter::where('created_by', $creatorId)->findOrFail($data['production_work_center_id']);

        if (!empty($data['production_order_id'])) {
            ProductionOrder::where('created_by', $creatorId)->findOrFail($data['production_order_id']);
        }

        $data['quantity'] = $data['quantity'] ?? 0;
        $data['downtime_minutes'] = $data['downtime_minutes'] ?? 0;
        $data['created_by'] = $creatorId;

        ProductionShopfloorEvent::create($data);

        return redirect()->route('production.planning')->with('success', __('Shopfloor event recorded.'));
    }

    public function analytics()
    {
        if (!\Auth::user()->can('show industrial planning')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creatorId = \Auth::user()->creatorId();

        $orderMetrics = ProductionOrder::where('created_by', $creatorId)
            ->with(['product', 'workCenter'])
            ->latest('planned_start_date')
            ->limit(12)
            ->get()
            ->map(function ($order) {
                $plannedQty = max((float) $order->quantity_planned, 1);
                $completion = round(((float) $order->quantity_produced / $plannedQty) * 100, 1);

                return [
                    'order' => $order,
                    'completion' => min($completion, 100),
                ];
            });

        $operationDelays = ProductionOrderOperation::where('created_by', $creatorId)
            ->with(['productionOrder.product', 'workCenter'])
            ->whereNotNull('planned_minutes')
            ->where('planned_minutes', '>', 0)
            ->orderByDesc(DB::raw('actual_minutes - planned_minutes'))
            ->limit(10)
            ->get();

        $laborPerformance = ProductionTimeLog::where('created_by', $creatorId)
            ->leftJoin('employees', 'employees.id', '=', 'production_time_logs.employee_id')
            ->select(
                'production_time_logs.employee_id',
                DB::raw("COALESCE(employees.name, 'Unassigned') as employee_name"),
                DB::raw('sum(minutes) as total_minutes'),
                DB::raw('count(*) as log_count')
            )
            ->groupBy('production_time_logs.employee_id', 'employees.name')
            ->orderByDesc('total_minutes')
            ->limit(10)
            ->get();

        $shopfloorTimeline = ProductionShopfloorEvent::where('created_by', $creatorId)
            ->with(['workCenter', 'order.product'])
            ->latest('happened_at')
            ->limit(20)
            ->get();

        $qualitySummary = ProductionQualityCheck::where('created_by', $creatorId)
            ->select('result', DB::raw('count(*) as total'))
            ->groupBy('result')
            ->pluck('total', 'result');

        $maintenanceImpact = IndustrialMaintenanceOrder::where('created_by', $creatorId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('production.planning.analytics', compact(
            'orderMetrics',
            'operationDelays',
            'laborPerformance',
            'shopfloorTimeline',
            'qualitySummary',
            'maintenanceImpact'
        ));
    }

    public function realtime()
    {
        if (!\Auth::user()->can('show industrial planning')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creatorId = \Auth::user()->creatorId();

        $workCenters = ProductionWorkCenter::where('created_by', $creatorId)
            ->with(['resource'])
            ->orderBy('name')
            ->get();

        $recentEvents = ProductionShopfloorEvent::where('created_by', $creatorId)
            ->with(['workCenter', 'order.product', 'employee'])
            ->latest('happened_at')
            ->get()
            ->groupBy('production_work_center_id');

        $liveBoard = $workCenters->map(function ($center) use ($recentEvents) {
            $lastEvent = optional($recentEvents->get($center->id))->first();

            return [
                'work_center' => $center,
                'last_event' => $lastEvent,
                'status' => $lastEvent?->status ?: 'idle',
                'event_type' => $lastEvent?->event_type ?: 'status',
                'happened_at' => $lastEvent?->happened_at,
            ];
        });

        $activeOrders = ProductionOrder::where('created_by', $creatorId)
            ->with(['product', 'workCenter'])
            ->whereIn('status', ['planned', 'in_progress'])
            ->orderBy('priority')
            ->orderBy('planned_start_date')
            ->limit(15)
            ->get();

        $timeline = ProductionShopfloorEvent::where('created_by', $creatorId)
            ->with(['workCenter', 'order.product', 'employee'])
            ->latest('happened_at')
            ->limit(25)
            ->get();

        $summary = ProductionShopfloorEvent::where('created_by', $creatorId)
            ->where('happened_at', '>=', Carbon::now()->subHours(8))
            ->select('event_type', DB::raw('count(*) as total'))
            ->groupBy('event_type')
            ->pluck('total', 'event_type');

        return view('production.planning.realtime', compact('liveBoard', 'activeOrders', 'timeline', 'summary'));
    }

    public function businessIntelligence()
    {
        if (!\Auth::user()->can('show industrial planning')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creatorId = \Auth::user()->creatorId();

        $orders = ProductionOrder::where('created_by', $creatorId)->get();
        $plannedHours = (float) $orders->sum('planned_machine_hours');
        $plannedLaborHours = (float) $orders->sum('planned_labor_hours');
        $completedOrders = $orders->filter(fn ($order) => (float) $order->quantity_planned > 0 && (float) $order->quantity_produced >= (float) $order->quantity_planned)->count();
        $completionRate = $orders->count() > 0 ? round(($completedOrders / $orders->count()) * 100, 1) : 0;

        $timeLogs = ProductionTimeLog::where('created_by', $creatorId)->get();
        $actualMachineHours = round($timeLogs->sum('minutes') / 60, 2);
        $downtimeMinutes = (int) ProductionShopfloorEvent::where('created_by', $creatorId)
            ->where('event_type', 'downtime')
            ->sum('downtime_minutes');

        $qualitySummary = ProductionQualityCheck::where('created_by', $creatorId)
            ->select('result', DB::raw('count(*) as total'))
            ->groupBy('result')
            ->pluck('total', 'result');

        $costMix = IndustrialCostRecord::where('created_by', $creatorId)
            ->select('cost_type', DB::raw('sum(amount) as total'))
            ->groupBy('cost_type')
            ->pluck('total', 'cost_type');

        $topBottlenecks = ProductionWorkCenter::where('created_by', $creatorId)
            ->withCount(['productionOrders as active_orders_count' => function ($query) {
                $query->whereIn('status', ['draft', 'planned', 'in_progress']);
            }])
            ->orderByDesc('active_orders_count')
            ->limit(8)
            ->get();

        $scheduleRisk = $orders->map(function ($order) {
            $plannedQty = max((float) $order->quantity_planned, 1);
            $completion = round(((float) $order->quantity_produced / $plannedQty) * 100, 1);
            $late = $order->planned_end_date && Carbon::parse($order->planned_end_date)->isPast() && $completion < 100;

            return [
                'order' => $order,
                'completion' => min($completion, 100),
                'late' => $late,
            ];
        })->sortByDesc('late')->take(10)->values();

        $kpis = [
            'planned_machine_hours' => round($plannedHours, 2),
            'actual_machine_hours' => $actualMachineHours,
            'planned_labor_hours' => round($plannedLaborHours, 2),
            'downtime_minutes' => $downtimeMinutes,
            'completion_rate' => $completionRate,
            'quality_passes' => (int) ($qualitySummary['pass'] ?? 0),
            'quality_holds' => (int) ($qualitySummary['hold'] ?? 0),
        ];

        return view('production.planning.business_intelligence', compact(
            'kpis',
            'costMix',
            'topBottlenecks',
            'scheduleRisk',
            'qualitySummary'
        ));
    }
}
