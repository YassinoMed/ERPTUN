<?php

namespace App\Services\Core;

use App\Models\Security\IpRestriction;
use App\Models\SensitiveAccessLog;
use App\Models\User;
use App\Models\UserSessionLog;
use Illuminate\Http\Request;

class SecurityAccessService
{
    public function isIpAllowed(User $user): bool
    {
        return IpRestriction::isIpAllowed($user->id);
    }

    public function startSession(User $user, Request $request): ?UserSessionLog
    {
        $sessionId = $request->session()->getId();
        if (blank($sessionId)) {
            return null;
        }

        return UserSessionLog::updateOrCreate([
            'session_id' => $sessionId,
        ], [
            'user_id' => $user->id,
            'created_by' => $user->creatorId(),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 65535),
            'login_at' => now(),
            'last_seen_at' => now(),
            'logout_at' => null,
            'is_active' => true,
        ]);
    }

    public function touchSession(?User $user, Request $request): void
    {
        if (! $user) {
            return;
        }

        UserSessionLog::query()
            ->where('session_id', $request->session()->getId())
            ->where('user_id', $user->id)
            ->update([
                'last_seen_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 65535),
            ]);
    }

    public function endSession(?User $user, ?string $sessionId): void
    {
        if (! $user || blank($sessionId)) {
            return;
        }

        UserSessionLog::query()
            ->where('session_id', $sessionId)
            ->where('user_id', $user->id)
            ->update([
                'logout_at' => now(),
                'last_seen_at' => now(),
                'is_active' => false,
            ]);
    }

    public function revokeSession(UserSessionLog $sessionLog): void
    {
        $sessionLog->update([
            'logout_at' => now(),
            'last_seen_at' => now(),
            'is_active' => false,
        ]);
    }

    public function revokeUserSessions(User $user, int $createdBy): int
    {
        return UserSessionLog::query()
            ->where('created_by', $createdBy)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->update([
                'logout_at' => now(),
                'last_seen_at' => now(),
                'is_active' => false,
            ]);
    }

    public function revokeAllSessionsForOwner(int $createdBy): int
    {
        return UserSessionLog::query()
            ->where('created_by', $createdBy)
            ->where('is_active', true)
            ->update([
                'logout_at' => now(),
                'last_seen_at' => now(),
                'is_active' => false,
            ]);
    }

    public function logSensitiveAccess(string $action, string $resourceType, $resourceId = null, array $context = [], ?User $user = null, ?Request $request = null): SensitiveAccessLog
    {
        $user = $user ?: auth()->user();
        $request = $request ?: request();

        return SensitiveAccessLog::create([
            'user_id' => $user?->id ?? 0,
            'created_by' => $user?->creatorId() ?? 0,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'route' => optional($request->route())->getName(),
            'ip_address' => $request?->ip(),
            'user_agent' => substr((string) $request?->userAgent(), 0, 65535),
            'context' => $context,
            'created_at' => now(),
        ]);
    }
}
