<?php

namespace App\Http\Controllers;

use App\Models\HotelUpsellConversion;
use App\Models\HotelUpsellOffer;
use App\Models\HotelUpsellPackage;
use App\Models\HotelUpsellPackageItem;
use App\Models\HotelUpsellService;
use App\Services\HotelUpsellSuggestionService;
use Illuminate\Http\Request;

class HotelUpsellController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage hotel upsell')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $services = HotelUpsellService::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->get();

        $packages = HotelUpsellPackage::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->with('items.service')
            ->latest('id')
            ->get();

        $offers = HotelUpsellOffer::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->limit(10)
            ->get();

        $conversions = HotelUpsellConversion::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->limit(10)
            ->get();

        return view('hotel/upsell', compact('services', 'packages', 'offers', 'conversions'));
    }

    public function storeService(Request $request)
    {
        if (!\Auth::user()->can('manage hotel upsell')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'currency' => 'nullable|string|max:10',
            'stock' => 'required|integer|min:0',
        ]);

        HotelUpsellService::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'EUR',
            'stock' => $data['stock'],
            'is_active' => true,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        return redirect()->route('hotel.upsell.index')->with('success', __('Service created.'));
    }

    public function storePackage(Request $request)
    {
        if (!\Auth::user()->can('manage hotel upsell')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'integer',
        ]);

        $package = HotelUpsellPackage::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => true,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        if (!empty($data['service_ids'])) {
            foreach ($data['service_ids'] as $serviceId) {
                HotelUpsellPackageItem::create([
                    'package_id' => $package->id,
                    'service_id' => $serviceId,
                    'quantity' => 1,
                ]);
            }
        }

        return redirect()->route('hotel.upsell.index')->with('success', __('Package created.'));
    }

    public function generateOffer(Request $request, HotelUpsellSuggestionService $service)
    {
        if (!\Auth::user()->can('manage hotel upsell')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'reservation_id' => 'nullable|integer',
            'customer_id' => 'nullable|integer',
            'room_type_id' => 'nullable|integer',
            'stay_length_min' => 'nullable|integer',
            'stay_length_max' => 'nullable|integer',
        ]);

        $service->generateOffer($data);

        return redirect()->route('hotel.upsell.index')->with('success', __('Offer generated.'));
    }

    public function convert(Request $request)
    {
        if (!\Auth::user()->can('manage hotel upsell')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'offer_id' => 'required|integer',
            'service_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $service = HotelUpsellService::query()
            ->where('id', $data['service_id'])
            ->where('created_by', \Auth::user()->creatorId())
            ->firstOrFail();

        $quantity = min($data['quantity'], $service->stock);
        $total = $quantity * $service->price;
        $service->update([
            'stock' => max(0, $service->stock - $quantity),
        ]);

        HotelUpsellConversion::create([
            'offer_id' => $data['offer_id'],
            'service_id' => $service->id,
            'quantity' => $quantity,
            'total_amount' => $total,
            'converted_at' => now(),
            'created_by' => \Auth::user()->creatorId(),
        ]);

        HotelUpsellOffer::where('id', $data['offer_id'])
            ->where('created_by', \Auth::user()->creatorId())
            ->update(['status' => 'converted']);

        return redirect()->route('hotel.upsell.index')->with('success', __('Conversion saved.'));
    }
}
