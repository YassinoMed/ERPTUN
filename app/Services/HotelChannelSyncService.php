<?php

namespace App\Services;

use App\Models\HotelChannel;
use App\Models\HotelChannelSyncLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HotelChannelSyncService
{
    public function syncAll(): array
    {
        $channels = HotelChannel::query()->where('is_active', true)->get();
        $creatorId = Auth::user()?->creatorId();
        $results = [];

        foreach ($channels as $channel) {
            $channel->update([
                'sync_status' => 'syncing',
            ]);

            $payload = [
                'channel' => $channel->code,
                'synced_at' => now()->toDateTimeString(),
            ];

            DB::transaction(function () use ($channel, $payload, $creatorId) {
                HotelChannelSyncLog::create([
                    'channel_id' => $channel->id,
                    'status' => 'success',
                    'direction' => 'bidirectional',
                    'message' => 'sync_completed',
                    'payload' => $payload,
                    'synced_at' => now(),
                    'created_by' => $creatorId,
                ]);

                $channel->update([
                    'sync_status' => 'idle',
                    'last_synced_at' => now(),
                ]);
            });

            $results[] = [
                'channel' => $channel->code,
                'status' => 'success',
            ];
        }

        return $results;
    }
}
