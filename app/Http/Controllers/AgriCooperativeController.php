<?php

namespace App\Http\Controllers;

use App\Models\AgriCoopMember;
use App\Models\AgriCooperative;
use App\Models\AgriHarvestDelivery;
use App\Models\AgriMemberPayout;
use App\Models\AgriRevenueDistribution;
use App\Services\AgriCooperativeService;
use Illuminate\Http\Request;

class AgriCooperativeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage agri cooperative')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $cooperatives = AgriCooperative::query()
            ->where('created_by', $creatorId)
            ->latest('id')
            ->get();

        $members = AgriCoopMember::query()
            ->where('created_by', $creatorId)
            ->latest('id')
            ->get();

        $deliveries = AgriHarvestDelivery::query()
            ->where('created_by', $creatorId)
            ->latest('delivery_date')
            ->limit(20)
            ->get();

        $distributions = AgriRevenueDistribution::query()
            ->where('created_by', $creatorId)
            ->latest('period_end')
            ->limit(10)
            ->get();

        $payouts = AgriMemberPayout::query()
            ->where('created_by', $creatorId)
            ->latest('id')
            ->limit(10)
            ->get();

        return view('agri/cooperatives', compact('cooperatives', 'members', 'deliveries', 'distributions', 'payouts'));
    }

    public function storeCooperative(Request $request)
    {
        if (!\Auth::user()->can('manage agri cooperative')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:100',
            'region' => 'nullable|string|max:191',
            'currency' => 'nullable|string|max:8',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['currency'] = $data['currency'] ?? 'USD';

        AgriCooperative::updateOrCreate(
            [
                'code' => $data['code'],
                'created_by' => $data['created_by'],
            ],
            $data
        );

        return redirect()->route('agri.cooperatives.index')->with('success', __('Cooperative saved.'));
    }

    public function storeMember(Request $request)
    {
        if (!\Auth::user()->can('manage agri cooperative')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'cooperative_id' => 'required|integer',
            'name' => 'required|string|max:191',
            'member_code' => 'required|string|max:100',
            'share_percent' => 'nullable|numeric',
            'advance_balance' => 'nullable|numeric',
            'contact_phone' => 'nullable|string|max:50',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['share_percent'] = $data['share_percent'] ?? 0;
        $data['advance_balance'] = $data['advance_balance'] ?? 0;

        AgriCoopMember::updateOrCreate(
            [
                'member_code' => $data['member_code'],
                'created_by' => $data['created_by'],
            ],
            $data
        );

        return redirect()->route('agri.cooperatives.index')->with('success', __('Member saved.'));
    }

    public function storeDelivery(Request $request)
    {
        if (!\Auth::user()->can('manage agri cooperative')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
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

        $data['created_by'] = \Auth::user()->creatorId();
        $data['unit'] = $data['unit'] ?? 'kg';
        $data['total_value'] = (float) $data['quantity'] * (float) $data['price_per_unit'];

        AgriHarvestDelivery::create($data);

        return redirect()->route('agri.cooperatives.index')->with('success', __('Delivery recorded.'));
    }

    public function createDistribution(Request $request, AgriCooperativeService $service)
    {
        if (!\Auth::user()->can('manage agri cooperative')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'cooperative_id' => 'required|integer',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'distribution_method' => 'nullable|string|max:32',
        ]);

        $service->createDistribution(
            (int) $data['cooperative_id'],
            $data['period_start'],
            $data['period_end'],
            $data['distribution_method'] ?? 'quantity'
        );

        return redirect()->route('agri.cooperatives.index')->with('success', __('Distribution created.'));
    }
}
