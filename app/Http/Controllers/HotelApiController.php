<?php

namespace App\Http\Controllers;

use App\Models\HotelHousekeepingTask;
use App\Models\HotelMaintenanceIssue;
use App\Models\HotelPriceRecommendation;
use App\Models\HotelUpsellConversion;
use App\Models\HotelUpsellOffer;
use App\Models\HotelUpsellService;
use App\Services\HotelChannelSyncService;
use App\Services\HotelUpsellSuggestionService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class HotelApiController extends Controller
{
    use ApiResponser;

    private function tokenAllows(Request $request, string $ability): bool
    {
        $token = $request->user()?->currentAccessToken();
        if (!$token) {
            return false;
        }

        return $request->user()->tokenCan('*') || $request->user()->tokenCan($ability);
    }

    public function channels(Request $request)
    {
        if (!$this->tokenAllows($request, 'hotel.channels.read')) {
            return $this->error('Forbidden', 403);
        }

        $channels = \App\Models\HotelChannel::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->get();

        return $this->success(['channels' => $channels], 'Channels retrieved.');
    }

    public function syncChannels(Request $request, HotelChannelSyncService $service)
    {
        if (!$this->tokenAllows($request, 'hotel.channels.sync')) {
            return $this->error('Forbidden', 403);
        }

        $result = $service->syncAll();

        return $this->success(['result' => $result], 'Sync completed.');
    }

    public function recommendations(Request $request)
    {
        if (!$this->tokenAllows($request, 'hotel.pricing.read')) {
            return $this->error('Forbidden', 403);
        }

        $recommendations = HotelPriceRecommendation::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('date')
            ->limit(20)
            ->get();

        return $this->success(['recommendations' => $recommendations], 'Recommendations retrieved.');
    }

    public function createHousekeepingTask(Request $request)
    {
        if (!$this->tokenAllows($request, 'hotel.housekeeping.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'room_id' => 'required|integer',
            'priority' => 'nullable|string|max:32',
            'assigned_to' => 'nullable|integer',
            'scheduled_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $task = HotelHousekeepingTask::create([
            'room_id' => $data['room_id'],
            'status' => 'pending',
            'priority' => $data['priority'] ?? 'normal',
            'assigned_to' => $data['assigned_to'] ?? null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $request->user()->creatorId(),
        ]);

        return $this->success(['task' => $task], 'Task created.');
    }

    public function createMaintenanceIssue(Request $request)
    {
        if (!$this->tokenAllows($request, 'hotel.housekeeping.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'room_id' => 'required|integer',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|max:32',
        ]);

        $issue = HotelMaintenanceIssue::create([
            'room_id' => $data['room_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'open',
            'priority' => $data['priority'] ?? 'normal',
            'reported_by' => $request->user()->id,
            'created_by' => $request->user()->creatorId(),
        ]);

        return $this->success(['issue' => $issue], 'Issue created.');
    }

    public function generateUpsellOffer(Request $request, HotelUpsellSuggestionService $service)
    {
        if (!$this->tokenAllows($request, 'hotel.upsell.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'reservation_id' => 'nullable|integer',
            'customer_id' => 'nullable|integer',
            'room_type_id' => 'nullable|integer',
            'stay_length_min' => 'nullable|integer',
            'stay_length_max' => 'nullable|integer',
        ]);

        $offer = $service->generateOffer($data);

        return $this->success(['offer' => $offer], 'Offer generated.');
    }

    public function convertUpsell(Request $request)
    {
        if (!$this->tokenAllows($request, 'hotel.upsell.write')) {
            return $this->error('Forbidden', 403);
        }

        $data = $request->validate([
            'offer_id' => 'required|integer',
            'service_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $service = HotelUpsellService::query()
            ->where('id', $data['service_id'])
            ->where('created_by', $request->user()->creatorId())
            ->firstOrFail();

        $quantity = min($data['quantity'], $service->stock);
        $total = $quantity * $service->price;
        $service->update(['stock' => max(0, $service->stock - $quantity)]);

        $conversion = HotelUpsellConversion::create([
            'offer_id' => $data['offer_id'],
            'service_id' => $service->id,
            'quantity' => $quantity,
            'total_amount' => $total,
            'converted_at' => now(),
            'created_by' => $request->user()->creatorId(),
        ]);

        HotelUpsellOffer::where('id', $data['offer_id'])
            ->where('created_by', $request->user()->creatorId())
            ->update(['status' => 'converted']);

        return $this->success(['conversion' => $conversion], 'Conversion saved.');
    }
}
