<?php

namespace App\Http\Controllers;

use App\Models\HotelDemandForecast;
use App\Models\HotelPriceRecommendation;
use App\Models\HotelPricingRule;
use App\Models\HotelRatePlan;
use App\Models\HotelRoomType;
use App\Services\HotelPricingService;
use Illuminate\Http\Request;

class HotelYieldController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage hotel pricing')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $roomTypes = HotelRoomType::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->get();

        $ratePlans = HotelRatePlan::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->get();

        $rules = HotelPricingRule::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->get();

        $forecasts = HotelDemandForecast::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('date')
            ->limit(10)
            ->get();

        $recommendations = HotelPriceRecommendation::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('date')
            ->limit(10)
            ->get();

        return view('hotel/yield_management', compact('roomTypes', 'ratePlans', 'rules', 'forecasts', 'recommendations'));
    }

    public function storeRule(Request $request)
    {
        if (!\Auth::user()->can('manage hotel pricing')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'room_type_id' => 'nullable|integer',
            'customer_segment' => 'nullable|string|max:191',
            'min_rate' => 'nullable|numeric',
            'max_rate' => 'nullable|numeric',
            'margin' => 'nullable|numeric',
            'occupancy_threshold' => 'nullable|numeric',
            'seasonality_multiplier' => 'nullable|numeric',
            'event_multiplier' => 'nullable|numeric',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['is_active'] = true;

        HotelPricingRule::create($data);

        return redirect()->route('hotel.yield.index')->with('success', __('Pricing rule created.'));
    }

    public function storeForecast(Request $request)
    {
        if (!\Auth::user()->can('manage hotel pricing')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'room_type_id' => 'required|integer',
            'date' => 'required|date',
            'demand_score' => 'nullable|numeric',
            'occupancy_rate' => 'nullable|numeric',
            'seasonal_factor' => 'nullable|numeric',
            'event_factor' => 'nullable|numeric',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();

        HotelDemandForecast::updateOrCreate(
            [
                'room_type_id' => $data['room_type_id'],
                'date' => $data['date'],
                'created_by' => \Auth::user()->creatorId(),
            ],
            $data
        );

        return redirect()->route('hotel.yield.index')->with('success', __('Forecast saved.'));
    }

    public function generate(Request $request, HotelPricingService $service)
    {
        if (!\Auth::user()->can('manage hotel pricing')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'room_type_id' => 'nullable|integer',
            'date' => 'nullable|date',
        ]);

        $service->generateRecommendations($data['room_type_id'] ?? null, $data['date'] ?? null);

        return redirect()->route('hotel.yield.index')->with('success', __('Recommendations generated.'));
    }
}
