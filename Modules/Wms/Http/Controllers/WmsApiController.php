<?php

namespace Modules\Wms\Http\Controllers;

use App\Models\StockReport;
use App\Models\WarehouseProduct;
use App\Models\warehouse;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Wms\Jobs\WmsEventJob;

class WmsApiController extends Controller
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

    public function warehouses(Request $request)
    {
        if (! $this->tokenAllows($request, 'wms.read')) {
            return $this->error('Forbidden', 403);
        }

        $warehouses = warehouse::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        WmsEventJob::dispatch([
            'event' => 'WmsWarehousesFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'warehouses' => $warehouses,
        ], 'Warehouses fetched successfully.');
    }

    public function warehouseShow(Request $request, warehouse $warehouse)
    {
        if (! $this->tokenAllows($request, 'wms.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $warehouse->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        return $this->success([
            'warehouse' => $warehouse,
        ], 'Warehouse fetched successfully.');
    }

    public function warehouseProducts(Request $request)
    {
        if (! $this->tokenAllows($request, 'wms.read')) {
            return $this->error('Forbidden', 403);
        }

        $warehouseProducts = WarehouseProduct::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['product', 'warehouse'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        WmsEventJob::dispatch([
            'event' => 'WmsWarehouseProductsFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'warehouse_products' => $warehouseProducts,
        ], 'Warehouse products fetched successfully.');
    }

    public function warehouseProductShow(Request $request, WarehouseProduct $warehouseProduct)
    {
        if (! $this->tokenAllows($request, 'wms.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $warehouseProduct->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        $warehouseProduct->load(['product', 'warehouse']);

        return $this->success([
            'warehouse_product' => $warehouseProduct,
        ], 'Warehouse product fetched successfully.');
    }

    public function stockReports(Request $request)
    {
        if (! $this->tokenAllows($request, 'wms.read')) {
            return $this->error('Forbidden', 403);
        }

        $reports = StockReport::query()
            ->where('created_by', $request->user()->creatorId())
            ->with('product')
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        WmsEventJob::dispatch([
            'event' => 'WmsStockReportsFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'stock_reports' => $reports,
        ], 'Stock reports fetched successfully.');
    }
}
