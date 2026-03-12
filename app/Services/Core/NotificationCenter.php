<?php

namespace App\Services\Core;

use App\Models\Notification;
use App\Models\NotificationPreference;
use Illuminate\Support\Facades\Schema;

class NotificationCenter
{
    public function create(int $userId, string $type, array $payload, array $context = []): ?Notification
    {
        if (! Schema::hasTable('notifications')) {
            return null;
        }

        if (! $this->isChannelEnabled($userId, $type, 'in_app', $context['created_by'] ?? null)) {
            return null;
        }

        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'data' => json_encode($payload),
            'is_read' => 0,
        ]);
    }

    public function isChannelEnabled(int $userId, string $type, string $channel = 'in_app', ?int $createdBy = null): bool
    {
        if (! Schema::hasTable('notification_preferences')) {
            return true;
        }

        $query = NotificationPreference::query()
            ->where('user_id', $userId)
            ->where('notification_type', $type);

        if ($createdBy !== null) {
            $query->where('created_by', $createdBy);
        }

        $preference = $query->first();

        if (! $preference) {
            return true;
        }

        return (bool) ($preference->{$channel} ?? true);
    }
}
