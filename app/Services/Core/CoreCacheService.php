<?php

namespace App\Services\Core;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Department;
use App\Models\ProductService;
use App\Models\UserAccessScope;
use App\Models\Vender;
use App\Models\warehouse;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class CoreCacheService
{
    public function warmForOwner(int $creatorId): void
    {
        Cache::put($this->key('branches', $creatorId), Branch::query()->where('created_by', $creatorId)->orderBy('name')->pluck('name', 'id')->toArray(), now()->addMinutes(30));
        Cache::put($this->key('warehouses', $creatorId), warehouse::query()->where('created_by', $creatorId)->orderBy('name')->pluck('name', 'id')->toArray(), now()->addMinutes(30));
        Cache::put($this->key('departments', $creatorId), Department::query()->where('created_by', $creatorId)->orderBy('name')->pluck('name', 'id')->toArray(), now()->addMinutes(30));
        Cache::put($this->key('services', $creatorId), ProductService::query()->where('created_by', $creatorId)->where('type', 'service')->orderBy('name')->pluck('name', 'id')->toArray(), now()->addMinutes(30));
        Cache::put($this->key('customers', $creatorId), Customer::query()->where('created_by', $creatorId)->whereNull('archived_at')->orderBy('name')->pluck('name', 'id')->toArray(), now()->addMinutes(30));
        Cache::put($this->key('venders', $creatorId), Vender::query()->where('created_by', $creatorId)->whereNull('archived_at')->orderBy('name')->pluck('name', 'id')->toArray(), now()->addMinutes(30));
        Cache::put($this->key('products', $creatorId), ProductService::query()->where('created_by', $creatorId)->where('type', 'product')->whereNull('archived_at')->orderBy('name')->pluck('name', 'id')->toArray(), now()->addMinutes(30));
        Cache::put($this->key('access_scopes', $creatorId), UserAccessScope::query()->where('created_by', $creatorId)->count(), now()->addMinutes(30));
        Cache::put($this->key('permissions', $creatorId), Permission::query()->orderBy('name')->pluck('name')->all(), now()->addMinutes(30));
    }

    public function flushForOwner(int $creatorId): void
    {
        foreach (['branches', 'warehouses', 'departments', 'services', 'customers', 'venders', 'products', 'access_scopes', 'permissions'] as $segment) {
            Cache::forget($this->key($segment, $creatorId));
        }
    }

    public function snapshot(int $creatorId): array
    {
        return [
            'branches' => count((array) Cache::get($this->key('branches', $creatorId), [])),
            'warehouses' => count((array) Cache::get($this->key('warehouses', $creatorId), [])),
            'departments' => count((array) Cache::get($this->key('departments', $creatorId), [])),
            'services' => count((array) Cache::get($this->key('services', $creatorId), [])),
            'customers' => count((array) Cache::get($this->key('customers', $creatorId), [])),
            'venders' => count((array) Cache::get($this->key('venders', $creatorId), [])),
            'products' => count((array) Cache::get($this->key('products', $creatorId), [])),
            'access_scopes' => (int) Cache::get($this->key('access_scopes', $creatorId), 0),
            'permissions' => count((array) Cache::get($this->key('permissions', $creatorId), [])),
        ];
    }

    private function key(string $segment, int $creatorId): string
    {
        return 'core_cache_' . $segment . '_' . $creatorId;
    }
}
