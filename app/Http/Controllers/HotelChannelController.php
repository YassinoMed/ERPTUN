<?php

namespace App\Http\Controllers;

use App\Models\HotelChannel;
use App\Models\HotelChannelSyncLog;
use App\Models\HotelReservation;
use App\Services\HotelChannelSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HotelChannelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage hotel channel')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $channels = HotelChannel::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->get();

        $reservations = HotelReservation::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->selectRaw('channel_id, count(*) as total, sum(total_amount) as revenue')
            ->groupBy('channel_id')
            ->get()
            ->keyBy('channel_id');

        $alerts = $channels->filter(function ($channel) {
            if ($channel->sync_status !== 'idle') {
                return true;
            }
            if ($channel->last_synced_at === null) {
                return true;
            }
            return $channel->last_synced_at->lt(Carbon::now()->subHours(2));
        });

        $logs = HotelChannelSyncLog::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->limit(10)
            ->get();

        return view('hotel/channel_manager', compact('channels', 'reservations', 'alerts', 'logs'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('manage hotel channel')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:64',
            'is_active' => 'nullable|boolean',
        ]);

        HotelChannel::updateOrCreate(
            [
                'code' => $data['code'],
                'created_by' => \Auth::user()->creatorId(),
            ],
            [
                'name' => $data['name'],
                'is_active' => $request->boolean('is_active'),
                'created_by' => \Auth::user()->creatorId(),
            ]
        );

        return redirect()->route('hotel.channels.index')->with('success', __('Channel updated.'));
    }

    public function sync(HotelChannelSyncService $service)
    {
        if (!\Auth::user()->can('manage hotel channel')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $service->syncAll();

        return redirect()->route('hotel.channels.index')->with('success', __('Sync completed.'));
    }
}
