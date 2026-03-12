<?php

namespace App\Services\Core;

use App\Models\Branch;
use App\Models\Department;
use App\Models\ProductService;
use App\Models\User;
use App\Models\UserAccessScope;
use App\Models\warehouse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AccessScopeService
{
    public const SUPPORTED_TYPES = [
        'branch',
        'warehouse',
        'department',
        'service',
    ];

    public function getSupportedTypes(): array
    {
        return self::SUPPORTED_TYPES;
    }

    public function availableScopes(int $creatorId): array
    {
        return Cache::remember($this->cacheKey($creatorId), now()->addMinutes(15), function () use ($creatorId) {
            return [
                'branch' => Branch::query()
                    ->where('created_by', $creatorId)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray(),
                'warehouse' => warehouse::query()
                    ->where('created_by', $creatorId)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray(),
                'department' => Department::query()
                    ->where('created_by', $creatorId)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray(),
                'service' => ProductService::query()
                    ->where('created_by', $creatorId)
                    ->where('type', 'service')
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray(),
            ];
        });
    }

    public function groupedScopesForCreator(int $creatorId): Collection
    {
        return UserAccessScope::query()
            ->with('user')
            ->where('created_by', $creatorId)
            ->orderBy('user_id')
            ->orderBy('scope_type')
            ->orderBy('scope_id')
            ->get()
            ->groupBy('user_id');
    }

    public function scopedIds(User $user, string $scopeType): array
    {
        if (! in_array($scopeType, self::SUPPORTED_TYPES, true)) {
            return [];
        }

        return UserAccessScope::query()
            ->where('created_by', $user->creatorId())
            ->where('user_id', $user->id)
            ->where('scope_type', $scopeType)
            ->pluck('scope_id')
            ->map(static fn ($id) => (int) $id)
            ->all();
    }

    public function hasRestrictedScope(User $user, string $scopeType): bool
    {
        return count($this->scopedIds($user, $scopeType)) > 0;
    }

    public function filterOwnedQuery(Builder $query, User $user, string $scopeType, string $column = 'id'): Builder
    {
        $query->where('created_by', $user->creatorId());
        $ids = $this->scopedIds($user, $scopeType);

        if (! empty($ids)) {
            $query->whereIn($column, $ids);
        }

        return $query;
    }

    public function assertScopeAccess(User $user, string $scopeType, ?int $scopeId): void
    {
        if (empty($scopeId)) {
            return;
        }

        $ids = $this->scopedIds($user, $scopeType);

        if (! empty($ids) && ! in_array((int) $scopeId, $ids, true)) {
            abort(403, __('Permission denied.'));
        }
    }

    public function syncScope(User $actor, int $userId, string $scopeType, array $scopeIds, ?string $notes = null): void
    {
        if (! in_array($scopeType, self::SUPPORTED_TYPES, true)) {
            return;
        }

        $scopeIds = array_values(array_unique(array_map('intval', array_filter($scopeIds))));

        UserAccessScope::query()
            ->where('created_by', $actor->creatorId())
            ->where('user_id', $userId)
            ->where('scope_type', $scopeType)
            ->whereNotIn('scope_id', $scopeIds ?: [0])
            ->delete();

        foreach ($scopeIds as $scopeId) {
            UserAccessScope::query()->updateOrCreate([
                'user_id' => $userId,
                'created_by' => $actor->creatorId(),
                'scope_type' => $scopeType,
                'scope_id' => $scopeId,
            ], [
                'assigned_by' => $actor->id,
                'notes' => $notes,
            ]);
        }

        $this->flushCache($actor->creatorId());
    }

    public function flushCache(int $creatorId): void
    {
        Cache::forget($this->cacheKey($creatorId));
    }

    private function cacheKey(int $creatorId): string
    {
        return 'core_access_scopes_' . $creatorId;
    }
}
