<?php

namespace Modules\Production\Http\Controllers;

use App\Models\ProductionBom;
use App\Models\ProductionOrder;
use App\Models\ProductionWorkCenter;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Production\Jobs\ProductionEventJob;

class ProductionApiController extends Controller
{
    use ApiResponser;

    private function tokenAllows(Request $request, string $ability): bool
    {
        $token = $request->user()?->currentAccessToken();
        if (! $token) {
            return false;
        }

        return $request->user()->tokenCan('*') || $request->user()->tokenCan($ability);
    }

    public function workCenters(Request $request)
    {
        if (! $this->tokenAllows($request, 'production.read')) {
            return $this->error('Forbidden', 403);
        }

        $workCenters = ProductionWorkCenter::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        ProductionEventJob::dispatch([
            'event' => 'ProductionWorkCentersFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'work_centers' => $workCenters,
        ], 'Work centers fetched successfully.');
    }

    public function boms(Request $request)
    {
        if (! $this->tokenAllows($request, 'production.read')) {
            return $this->error('Forbidden', 403);
        }

        $boms = ProductionBom::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['product', 'activeVersion'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        ProductionEventJob::dispatch([
            'event' => 'ProductionBomsFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'boms' => $boms,
        ], 'BOMs fetched successfully.');
    }

    public function orders(Request $request)
    {
        if (! $this->tokenAllows($request, 'production.read')) {
            return $this->error('Forbidden', 403);
        }

        $orders = ProductionOrder::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['product', 'workCenter', 'warehouse'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        ProductionEventJob::dispatch([
            'event' => 'ProductionOrdersFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'orders' => $orders,
        ], 'Production orders fetched successfully.');
    }

    public function orderShow(Request $request, ProductionOrder $productionOrder)
    {
        if (! $this->tokenAllows($request, 'production.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $productionOrder->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        $productionOrder->load([
            'product',
            'bomVersion',
            'warehouse',
            'workCenter',
            'employee',
            'operations',
            'materials',
            'qualityChecks',
        ]);

        ProductionEventJob::dispatch([
            'event' => 'ProductionOrderFetched',
            'user_id' => $request->user()->id,
            'order_id' => $productionOrder->id,
        ]);

        return $this->success([
            'order' => $productionOrder,
        ], 'Production order fetched successfully.');
    }
}
