<?php

namespace App\Http\Controllers;

use App\Models\AgriCertificate;
use App\Models\AgriCooperative;
use App\Models\AgriCropPlan;
use App\Models\AgriHedgePosition;
use App\Models\AgriHarvestDelivery;
use App\Models\AgriLot;
use App\Models\AgriPriceIndex;
use App\Models\AgriPurchaseContract;
use App\Models\AgriTraceEvent;
use App\Services\AgriCooperativeService;
use App\Services\AgriHedgingService;
use App\Services\AgriTraceabilityService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class AgriApiController extends Controller
{
    use ApiResponser;

    private function tokenAllows(Request $request, string $ability): bool
    {
        $token = $request->user()?->currentAccessToken();
        if (!$token) {
            return false;
        }

        return $request->user()->tokenCan('*') || $request->user()->tokenCan($ability);
    }

    public function lots(Request $request)
    {
        if (!$this->tokenAllows($request, 'agri.traceability.read')) {
            return $this->error('Forbidden', 403);
        }

        $lots = AgriLot::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->get();

        return $this->success(['lots' => $lots], 'Lots retrieved.');
    }

    public function createLot(Request $request)
    {
        if (!$this->tokenAllows($request, 'agri.traceability.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'code' => 'required|string|max:100',
            'name' => 'required|string|max:191',
            'crop_type' => 'required|string|max:191',
            'harvest_date' => 'nullable|date',
            'quantity' => 'nullable|numeric',
            'unit' => 'nullable|string|max:32',
            'status' => 'nullable|string|max:32',
        ]);

        $data['created_by'] = $request->user()->creatorId();
        $data['unit'] = $data['unit'] ?? 'kg';
        $data['status'] = $data['status'] ?? 'active';

        $lot = AgriLot::updateOrCreate(
            [
                'code' => $data['code'],
                'created_by' => $data['created_by'],
            ],
            $data
        );

        return $this->success(['lot' => $lot], 'Lot saved.');
    }

    public function traceEvents(Request $request, int $lotId)
    {
        if (!$this->tokenAllows($request, 'agri.traceability.read')) {
            return $this->error('Forbidden', 403);
        }

        $events = AgriTraceEvent::query()
            ->where('lot_id', $lotId)
            ->where('created_by', $request->user()->creatorId())
            ->orderBy('occurred_at')
            ->get();

        return $this->success(['events' => $events], 'Events retrieved.');
    }

    public function createTraceEvent(Request $request, AgriTraceabilityService $service)
    {
        if (!$this->tokenAllows($request, 'agri.traceability.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'lot_id' => 'required|integer',
            'step' => 'required|string|max:191',
            'location' => 'nullable|string|max:191',
            'actor' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
            'occurred_at' => 'nullable|date',
        ]);

        $lot = AgriLot::query()
            ->where('created_by', $request->user()->creatorId())
            ->findOrFail($data['lot_id']);

        $event = $service->createTraceEvent($lot, $data);

        return $this->success(['event' => $event], 'Event created.');
    }

    public function issueCertificate(Request $request, AgriTraceabilityService $service)
    {
        if (!$this->tokenAllows($request, 'agri.traceability.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'lot_id' => 'required|integer',
        ]);

        $lot = AgriLot::query()
            ->where('created_by', $request->user()->creatorId())
            ->findOrFail($data['lot_id']);

        $certificate = $service->issueCertificate($lot);

        return $this->success(['certificate' => $certificate], 'Certificate issued.');
    }

    public function cropPlans(Request $request)
    {
        if (!$this->tokenAllows($request, 'agri.planning.read')) {
            return $this->error('Forbidden', 403);
        }

        $plans = AgriCropPlan::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('start_date')
            ->limit(20)
            ->get();

        return $this->success(['plans' => $plans], 'Plans retrieved.');
    }

    public function createCropPlan(Request $request)
    {
        if (!$this->tokenAllows($request, 'agri.planning.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'parcel_id' => 'required|integer',
            'crop_name' => 'required|string|max:191',
            'variety' => 'nullable|string|max:191',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|string|max:32',
            'expected_yield' => 'nullable|numeric',
            'yield_unit' => 'nullable|string|max:16',
            'notes' => 'nullable|string',
        ]);

        $data['created_by'] = $request->user()->creatorId();
        $data['status'] = $data['status'] ?? 'planned';
        $data['yield_unit'] = $data['yield_unit'] ?? 'kg';

        $plan = AgriCropPlan::create($data);

        return $this->success(['plan' => $plan], 'Plan created.');
    }

    public function cooperatives(Request $request)
    {
        if (!$this->tokenAllows($request, 'agri.cooperative.read')) {
            return $this->error('Forbidden', 403);
        }

        $cooperatives = AgriCooperative::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->get();

        return $this->success(['cooperatives' => $cooperatives], 'Cooperatives retrieved.');
    }

    public function createDelivery(Request $request)
    {
        if (!$this->tokenAllows($request, 'agri.cooperative.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'cooperative_id' => 'required|integer',
            'member_id' => 'required|integer',
            'lot_id' => 'nullable|integer',
            'crop_type' => 'required|string|max:191',
            'quantity' => 'required|numeric',
            'unit' => 'nullable|string|max:16',
            'delivery_date' => 'required|date',
            'price_per_unit' => 'required|numeric',
        ]);

        $data['created_by'] = $request->user()->creatorId();
        $data['unit'] = $data['unit'] ?? 'kg';
        $data['total_value'] = (float) $data['quantity'] * (float) $data['price_per_unit'];

        $delivery = AgriHarvestDelivery::create($data);

        return $this->success(['delivery' => $delivery], 'Delivery created.');
    }

    public function createDistribution(Request $request, AgriCooperativeService $service)
    {
        if (!$this->tokenAllows($request, 'agri.cooperative.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'cooperative_id' => 'required|integer',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'distribution_method' => 'nullable|string|max:32',
        ]);

        $distribution = $service->createDistribution(
            (int) $data['cooperative_id'],
            $data['period_start'],
            $data['period_end'],
            $data['distribution_method'] ?? 'quantity'
        );

        return $this->success(['distribution' => $distribution], 'Distribution created.');
    }

    public function contracts(Request $request)
    {
        if (!$this->tokenAllows($request, 'agri.hedging.read')) {
            return $this->error('Forbidden', 403);
        }

        $contracts = AgriPurchaseContract::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->get();

        return $this->success(['contracts' => $contracts], 'Contracts retrieved.');
    }

    public function createHedge(Request $request, AgriHedgingService $service)
    {
        if (!$this->tokenAllows($request, 'agri.hedging.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'contract_id' => 'required|integer',
            'instrument' => 'nullable|string|max:64',
            'position_type' => 'nullable|string|max:32',
            'quantity' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'opened_at' => 'nullable|date',
            'closed_at' => 'nullable|date',
            'status' => 'nullable|string|max:32',
            'hedge_ratio' => 'nullable|numeric',
        ]);

        $contract = AgriPurchaseContract::query()
            ->where('created_by', $request->user()->creatorId())
            ->findOrFail($data['contract_id']);

        $hedge = $service->createHedgePosition($contract, $data);

        return $this->success(['hedge' => $hedge], 'Hedge created.');
    }

    public function createPriceIndex(Request $request)
    {
        if (!$this->tokenAllows($request, 'agri.hedging.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'crop_type' => 'required|string|max:191',
            'market' => 'nullable|string|max:64',
            'price' => 'required|numeric',
            'currency' => 'nullable|string|max:8',
            'recorded_at' => 'required|date',
        ]);

        $data['created_by'] = $request->user()->creatorId();
        $data['currency'] = $data['currency'] ?? 'USD';

        $index = AgriPriceIndex::create($data);

        return $this->success(['index' => $index], 'Index created.');
    }
}
