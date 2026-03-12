<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next, string $tenantRouteKey = 'tenant'): Response
    {
        $user = $request->user();
        $tenantValue = $request->route($tenantRouteKey);
        $tenantId = (int) (is_object($tenantValue) ? ($tenantValue->id ?? 0) : $tenantValue);

        if (! $user || ! $tenantId) {
            abort(403, 'Tenant context missing.');
        }

        $ownerId = method_exists($user, 'creatorId')
            ? (int) $user->creatorId()
            : (int) ($user->created_by ?? $user->id);

        $hasCrossTenantAccess = ($user->type ?? null) === 'super admin'
            && $user->tokenCan('tenants.manage');

        $hasAccess = Cache::remember(
            sprintf('user:%d:tenant:%d:access', $user->id, $tenantId),
            now()->addMinutes(5),
            fn () => $hasCrossTenantAccess || in_array($tenantId, array_filter([$ownerId, (int) $user->id]), true)
        );

        if (! $hasAccess) {
            abort(403, 'You do not belong to this tenant.');
        }

        $request->attributes->set('tenant_id', $tenantId);

        return $next($request);
    }
}
