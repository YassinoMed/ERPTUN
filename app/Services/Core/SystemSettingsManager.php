<?php

namespace App\Services\Core;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemSettingsManager
{
    public function allForOwner(int $ownerId): array
    {
        if (! Schema::hasTable('settings')) {
            return [];
        }

        return Cache::remember($this->cacheKey($ownerId), now()->addMinutes(10), function () use ($ownerId) {
            return DB::table('settings')
                ->where('created_by', $ownerId)
                ->pluck('value', 'name')
                ->toArray();
        });
    }

    public function get(int $ownerId, string $key, mixed $default = null): mixed
    {
        $settings = $this->allForOwner($ownerId);

        return $settings[$key] ?? $default;
    }

    public function upsert(array $values, int $ownerId, ?array $allowedKeys = null): array
    {
        if (! Schema::hasTable('settings')) {
            return [];
        }

        $values = array_filter(
            $values,
            static fn ($value, $key) => $allowedKeys === null || in_array($key, $allowedKeys, true),
            ARRAY_FILTER_USE_BOTH
        );

        if (empty($values)) {
            return [];
        }

        $timestamp = now();
        $rows = [];

        foreach ($values as $name => $value) {
            $rows[] = [
                'name' => $name,
                'value' => is_scalar($value) || $value === null ? (string) $value : json_encode($value),
                'created_by' => $ownerId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        DB::table('settings')->upsert(
            $rows,
            ['name', 'created_by'],
            ['value', 'updated_at']
        );

        $this->forget($ownerId);

        return array_column($rows, 'name');
    }

    public function forget(int $ownerId): void
    {
        Cache::forget($this->cacheKey($ownerId));
    }

    private function cacheKey(int $ownerId): string
    {
        return 'core:settings:owner:'.$ownerId;
    }
}
