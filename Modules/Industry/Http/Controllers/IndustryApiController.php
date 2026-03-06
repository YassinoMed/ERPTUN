<?php

namespace Modules\Industry\Http\Controllers;

use App\Models\AgriCertificate;
use App\Models\AgriCoopMember;
use App\Models\AgriCooperative;
use App\Models\AgriCropPlan;
use App\Models\AgriHarvestDelivery;
use App\Models\AgriHedgePosition;
use App\Models\AgriLot;
use App\Models\AgriMemberPayout;
use App\Models\AgriParcel;
use App\Models\AgriPriceIndex;
use App\Models\AgriPurchaseContract;
use App\Models\AgriRevenueDistribution;
use App\Models\AgriRotationRule;
use App\Models\AgriTraceEvent;
use App\Models\AgriWeatherAlert;
use App\Models\HotelReservation;
use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndustryApiController extends Controller
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

    public function hotelRoomTypes(Request $request)
    {
        if (! $this->tokenAllows($request, 'hotel.read')) {
            return $this->error('Forbidden', 403);
        }

        $types = HotelRoomType::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'room_types' => $types,
        ], 'Hotel room types fetched successfully.');
    }

    public function hotelRooms(Request $request)
    {
        if (! $this->tokenAllows($request, 'hotel.read')) {
            return $this->error('Forbidden', 403);
        }

        $rooms = HotelRoom::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'rooms' => $rooms,
        ], 'Hotel rooms fetched successfully.');
    }

    public function hotelReservations(Request $request)
    {
        if (! $this->tokenAllows($request, 'hotel.read')) {
            return $this->error('Forbidden', 403);
        }

        $reservations = HotelReservation::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'reservations' => $reservations,
        ], 'Hotel reservations fetched successfully.');
    }

    public function traceLots(Request $request)
    {
        if (! $this->tokenAllows($request, 'traceability.read')) {
            return $this->error('Forbidden', 403);
        }

        $lots = AgriLot::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'lots' => $lots,
        ], 'Traceability lots fetched successfully.');
    }

    public function traceEvents(Request $request)
    {
        if (! $this->tokenAllows($request, 'traceability.read')) {
            return $this->error('Forbidden', 403);
        }

        $events = AgriTraceEvent::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'trace_events' => $events,
        ], 'Traceability events fetched successfully.');
    }

    public function traceCertificates(Request $request)
    {
        if (! $this->tokenAllows($request, 'traceability.read')) {
            return $this->error('Forbidden', 403);
        }

        $certificates = AgriCertificate::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'certificates' => $certificates,
        ], 'Traceability certificates fetched successfully.');
    }

    public function cropParcels(Request $request)
    {
        if (! $this->tokenAllows($request, 'crop_planning.read')) {
            return $this->error('Forbidden', 403);
        }

        $parcels = AgriParcel::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'parcels' => $parcels,
        ], 'Crop parcels fetched successfully.');
    }

    public function cropPlans(Request $request)
    {
        if (! $this->tokenAllows($request, 'crop_planning.read')) {
            return $this->error('Forbidden', 403);
        }

        $plans = AgriCropPlan::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'crop_plans' => $plans,
        ], 'Crop plans fetched successfully.');
    }

    public function rotationRules(Request $request)
    {
        if (! $this->tokenAllows($request, 'crop_planning.read')) {
            return $this->error('Forbidden', 403);
        }

        $rules = AgriRotationRule::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'rotation_rules' => $rules,
        ], 'Rotation rules fetched successfully.');
    }

    public function weatherAlerts(Request $request)
    {
        if (! $this->tokenAllows($request, 'crop_planning.read')) {
            return $this->error('Forbidden', 403);
        }

        $alerts = AgriWeatherAlert::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'weather_alerts' => $alerts,
        ], 'Weather alerts fetched successfully.');
    }

    public function cooperatives(Request $request)
    {
        if (! $this->tokenAllows($request, 'cooperative.read')) {
            return $this->error('Forbidden', 403);
        }

        $coops = AgriCooperative::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'cooperatives' => $coops,
        ], 'Cooperatives fetched successfully.');
    }

    public function cooperativeMembers(Request $request)
    {
        if (! $this->tokenAllows($request, 'cooperative.read')) {
            return $this->error('Forbidden', 403);
        }

        $members = AgriCoopMember::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'members' => $members,
        ], 'Cooperative members fetched successfully.');
    }

    public function harvestDeliveries(Request $request)
    {
        if (! $this->tokenAllows($request, 'cooperative.read')) {
            return $this->error('Forbidden', 403);
        }

        $deliveries = AgriHarvestDelivery::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'deliveries' => $deliveries,
        ], 'Harvest deliveries fetched successfully.');
    }

    public function revenueDistributions(Request $request)
    {
        if (! $this->tokenAllows($request, 'cooperative.read')) {
            return $this->error('Forbidden', 403);
        }

        $distributions = AgriRevenueDistribution::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'distributions' => $distributions,
        ], 'Revenue distributions fetched successfully.');
    }

    public function memberPayouts(Request $request)
    {
        if (! $this->tokenAllows($request, 'cooperative.read')) {
            return $this->error('Forbidden', 403);
        }

        $payouts = AgriMemberPayout::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'payouts' => $payouts,
        ], 'Member payouts fetched successfully.');
    }

    public function purchaseContracts(Request $request)
    {
        if (! $this->tokenAllows($request, 'hedging.read')) {
            return $this->error('Forbidden', 403);
        }

        $contracts = AgriPurchaseContract::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'contracts' => $contracts,
        ], 'Purchase contracts fetched successfully.');
    }

    public function hedgePositions(Request $request)
    {
        if (! $this->tokenAllows($request, 'hedging.read')) {
            return $this->error('Forbidden', 403);
        }

        $positions = AgriHedgePosition::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'positions' => $positions,
        ], 'Hedge positions fetched successfully.');
    }

    public function priceIndices(Request $request)
    {
        if (! $this->tokenAllows($request, 'hedging.read')) {
            return $this->error('Forbidden', 403);
        }

        $indices = AgriPriceIndex::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'price_indices' => $indices,
        ], 'Price indices fetched successfully.');
    }
}
