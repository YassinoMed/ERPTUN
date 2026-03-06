<?php

namespace Modules\Operations\Http\Controllers;

use App\Models\BtpEquipmentMaintenance;
use App\Models\HotelMaintenanceIssue;
use App\Models\ProductionBom;
use App\Models\ProductionMaterialMove;
use App\Models\ProductionQualityCheck;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OperationsApiController extends Controller
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

    public function mrpBoms(Request $request)
    {
        if (! $this->tokenAllows($request, 'mrp.read')) {
            return $this->error('Forbidden', 403);
        }

        $boms = ProductionBom::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['product', 'activeVersion'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'boms' => $boms,
        ], 'MRP BOMs fetched successfully.');
    }

    public function mrpMaterialMoves(Request $request)
    {
        if (! $this->tokenAllows($request, 'mrp.read')) {
            return $this->error('Forbidden', 403);
        }

        $moves = ProductionMaterialMove::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['productionOrder', 'component', 'warehouse'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'material_moves' => $moves,
        ], 'MRP material moves fetched successfully.');
    }

    public function qualityChecks(Request $request)
    {
        if (! $this->tokenAllows($request, 'quality.read')) {
            return $this->error('Forbidden', 403);
        }

        $checks = ProductionQualityCheck::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'quality_checks' => $checks,
        ], 'Quality checks fetched successfully.');
    }

    public function maintenanceIssues(Request $request)
    {
        if (! $this->tokenAllows($request, 'maintenance.read')) {
            return $this->error('Forbidden', 403);
        }

        $issues = HotelMaintenanceIssue::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'issues' => $issues,
        ], 'Maintenance issues fetched successfully.');
    }

    public function maintenanceEquipment(Request $request)
    {
        if (! $this->tokenAllows($request, 'maintenance.read')) {
            return $this->error('Forbidden', 403);
        }

        $maintenances = BtpEquipmentMaintenance::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'equipments' => $maintenances,
        ], 'Equipment maintenance fetched successfully.');
    }
}
