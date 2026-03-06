<?php

namespace App\Http\Controllers;

use App\Models\AgriCropPlan;
use App\Models\AgriParcel;
use App\Models\AgriRotationRule;
use App\Models\AgriWeatherAlert;
use App\Services\AgriPlanningService;
use Illuminate\Http\Request;

class AgriPlanningController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index(Request $request, AgriPlanningService $service)
    {
        if (!\Auth::user()->can('manage agri planning')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $parcels = AgriParcel::query()
            ->where('created_by', $creatorId)
            ->latest('id')
            ->get();

        $plans = AgriCropPlan::query()
            ->where('created_by', $creatorId)
            ->latest('start_date')
            ->limit(20)
            ->get();

        $rotationRules = AgriRotationRule::query()
            ->where('created_by', $creatorId)
            ->latest('id')
            ->get();

        $weatherAlerts = AgriWeatherAlert::query()
            ->where('created_by', $creatorId)
            ->latest('alert_date')
            ->limit(10)
            ->get();

        $selectedParcelId = $request->get('parcel_id');
        $selectedParcel = $selectedParcelId ? $parcels->firstWhere('id', (int) $selectedParcelId) : $parcels->first();
        $rotationRecommendations = $selectedParcel ? $service->getRotationRecommendations($selectedParcel->id) : collect();

        return view('agri/planning', compact('parcels', 'plans', 'rotationRules', 'weatherAlerts', 'selectedParcel', 'rotationRecommendations'));
    }

    public function storeParcel(Request $request)
    {
        if (!\Auth::user()->can('manage agri planning')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:100',
            'area' => 'nullable|numeric',
            'area_unit' => 'nullable|string|max:16',
            'soil_type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:191',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['area_unit'] = $data['area_unit'] ?? 'ha';

        AgriParcel::updateOrCreate(
            [
                'code' => $data['code'],
                'created_by' => $data['created_by'],
            ],
            $data
        );

        return redirect()->route('agri.planning.index')->with('success', __('Parcel saved.'));
    }

    public function storePlan(Request $request)
    {
        if (!\Auth::user()->can('manage agri planning')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
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

        $data['created_by'] = \Auth::user()->creatorId();
        $data['status'] = $data['status'] ?? 'planned';
        $data['yield_unit'] = $data['yield_unit'] ?? 'kg';

        AgriCropPlan::create($data);

        return redirect()->route('agri.planning.index')->with('success', __('Crop plan created.'));
    }

    public function storeRotationRule(Request $request)
    {
        if (!\Auth::user()->can('manage agri planning')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'crop_name' => 'required|string|max:191',
            'follow_crop_name' => 'nullable|string|max:191',
            'min_gap_days' => 'nullable|integer',
            'recommendation' => 'nullable|string',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['min_gap_days'] = $data['min_gap_days'] ?? 0;

        AgriRotationRule::create($data);

        return redirect()->route('agri.planning.index')->with('success', __('Rotation rule saved.'));
    }

    public function storeWeatherAlert(Request $request)
    {
        if (!\Auth::user()->can('manage agri planning')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'parcel_id' => 'nullable|integer',
            'alert_type' => 'required|string|max:64',
            'severity' => 'nullable|string|max:32',
            'message' => 'required|string',
            'alert_date' => 'required|date',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['severity'] = $data['severity'] ?? 'medium';

        AgriWeatherAlert::create($data);

        return redirect()->route('agri.planning.index')->with('success', __('Weather alert saved.'));
    }
}
