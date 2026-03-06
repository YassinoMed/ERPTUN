<?php

namespace App\Http\Controllers;

use App\Models\AgriCooperative;
use App\Models\AgriHedgePosition;
use App\Models\AgriPriceIndex;
use App\Models\AgriPurchaseContract;
use App\Services\AgriHedgingService;
use Illuminate\Http\Request;

class AgriContractController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage agri hedging')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $cooperatives = AgriCooperative::query()
            ->where('created_by', $creatorId)
            ->latest('id')
            ->get();

        $contracts = AgriPurchaseContract::query()
            ->where('created_by', $creatorId)
            ->latest('id')
            ->get();

        $hedgePositions = AgriHedgePosition::query()
            ->where('created_by', $creatorId)
            ->latest('opened_at')
            ->limit(15)
            ->get();

        $priceIndices = AgriPriceIndex::query()
            ->where('created_by', $creatorId)
            ->latest('recorded_at')
            ->limit(15)
            ->get();

        return view('agri/contracts', compact('cooperatives', 'contracts', 'hedgePositions', 'priceIndices'));
    }

    public function storeContract(Request $request)
    {
        if (!\Auth::user()->can('manage agri hedging')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'cooperative_id' => 'nullable|integer',
            'contract_number' => 'required|string|max:191',
            'buyer_name' => 'required|string|max:191',
            'crop_type' => 'required|string|max:191',
            'quantity' => 'required|numeric',
            'unit' => 'nullable|string|max:16',
            'fixed_price' => 'required|numeric',
            'price_currency' => 'nullable|string|max:8',
            'delivery_start' => 'required|date',
            'delivery_end' => 'required|date|after_or_equal:delivery_start',
            'status' => 'nullable|string|max:32',
            'hedge_ratio' => 'nullable|numeric',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['unit'] = $data['unit'] ?? 'kg';
        $data['price_currency'] = $data['price_currency'] ?? 'USD';
        $data['status'] = $data['status'] ?? 'active';
        $data['hedge_ratio'] = $data['hedge_ratio'] ?? 0;

        AgriPurchaseContract::updateOrCreate(
            [
                'contract_number' => $data['contract_number'],
                'created_by' => $data['created_by'],
            ],
            $data
        );

        return redirect()->route('agri.contracts.index')->with('success', __('Contract saved.'));
    }

    public function storeHedge(Request $request, AgriHedgingService $service)
    {
        if (!\Auth::user()->can('manage agri hedging')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
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
            ->where('created_by', \Auth::user()->creatorId())
            ->findOrFail($data['contract_id']);

        $service->createHedgePosition($contract, $data);

        return redirect()->route('agri.contracts.index')->with('success', __('Hedge position created.'));
    }

    public function storePriceIndex(Request $request)
    {
        if (!\Auth::user()->can('manage agri hedging')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'crop_type' => 'required|string|max:191',
            'market' => 'nullable|string|max:64',
            'price' => 'required|numeric',
            'currency' => 'nullable|string|max:8',
            'recorded_at' => 'required|date',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['currency'] = $data['currency'] ?? 'USD';

        AgriPriceIndex::create($data);

        return redirect()->route('agri.contracts.index')->with('success', __('Price index saved.'));
    }
}
