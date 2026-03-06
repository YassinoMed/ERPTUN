<?php

namespace Modules\Btp\Http\Controllers;

use App\Models\BtpEquipment;
use App\Models\BtpEquipmentMaintenance;
use App\Models\BtpEquipmentUsage;
use App\Models\BtpPriceItem;
use App\Models\BtpPriceQuote;
use App\Models\BtpPriceQuoteItem;
use App\Models\BtpSitePhoto;
use App\Models\BtpSubcontractInvoice;
use App\Models\BtpSubcontractPayment;
use App\Models\BtpSubcontractor;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BtpApiController extends Controller
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

    public function sitePhotos(Request $request)
    {
        if (! $this->tokenAllows($request, 'btp_site_tracking.read')) {
            return $this->error('Forbidden', 403);
        }

        $photos = BtpSitePhoto::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'photos' => $photos,
        ], 'Site photos fetched successfully.');
    }

    public function subcontractors(Request $request)
    {
        if (! $this->tokenAllows($request, 'btp_subcontractors.read')) {
            return $this->error('Forbidden', 403);
        }

        $subcontractors = BtpSubcontractor::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'subcontractors' => $subcontractors,
        ], 'Subcontractors fetched successfully.');
    }

    public function subcontractInvoices(Request $request)
    {
        if (! $this->tokenAllows($request, 'btp_subcontractors.read')) {
            return $this->error('Forbidden', 403);
        }

        $invoices = BtpSubcontractInvoice::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'invoices' => $invoices,
        ], 'Subcontract invoices fetched successfully.');
    }

    public function subcontractPayments(Request $request)
    {
        if (! $this->tokenAllows($request, 'btp_subcontractors.read')) {
            return $this->error('Forbidden', 403);
        }

        $payments = BtpSubcontractPayment::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'payments' => $payments,
        ], 'Subcontract payments fetched successfully.');
    }

    public function priceItems(Request $request)
    {
        if (! $this->tokenAllows($request, 'btp_price_breakdowns.read')) {
            return $this->error('Forbidden', 403);
        }

        $items = BtpPriceItem::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'items' => $items,
        ], 'Price items fetched successfully.');
    }

    public function priceQuotes(Request $request)
    {
        if (! $this->tokenAllows($request, 'btp_price_breakdowns.read')) {
            return $this->error('Forbidden', 403);
        }

        $quotes = BtpPriceQuote::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'quotes' => $quotes,
        ], 'Price quotes fetched successfully.');
    }

    public function priceQuoteItems(Request $request)
    {
        if (! $this->tokenAllows($request, 'btp_price_breakdowns.read')) {
            return $this->error('Forbidden', 403);
        }

        $items = BtpPriceQuoteItem::query()
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'quote_items' => $items,
        ], 'Price quote items fetched successfully.');
    }

    public function equipments(Request $request)
    {
        if (! $this->tokenAllows($request, 'btp_equipment_control.read')) {
            return $this->error('Forbidden', 403);
        }

        $equipments = BtpEquipment::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'equipments' => $equipments,
        ], 'Equipments fetched successfully.');
    }

    public function equipmentUsages(Request $request)
    {
        if (! $this->tokenAllows($request, 'btp_equipment_control.read')) {
            return $this->error('Forbidden', 403);
        }

        $usages = BtpEquipmentUsage::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'usages' => $usages,
        ], 'Equipment usages fetched successfully.');
    }

    public function equipmentMaintenances(Request $request)
    {
        if (! $this->tokenAllows($request, 'btp_equipment_control.read')) {
            return $this->error('Forbidden', 403);
        }

        $maintenances = BtpEquipmentMaintenance::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'maintenances' => $maintenances,
        ], 'Equipment maintenances fetched successfully.');
    }
}
