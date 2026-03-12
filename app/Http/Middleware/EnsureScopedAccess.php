<?php

namespace App\Http\Middleware;

use App\Services\Core\AccessScopeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureScopedAccess
{
    public function __construct(
        private readonly AccessScopeService $accessScopes
    ) {
    }

    public function handle(Request $request, Closure $next, string $scopeType, string $routeKey = 'id'): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(403, 'Permission denied.');
        }

        $scopeValue = $request->route($routeKey);
        $scopeId = (int) (is_object($scopeValue) ? ($scopeValue->id ?? 0) : $scopeValue);
        $this->accessScopes->assertScopeAccess($user, $scopeType, $scopeId);

        return $next($request);
    }
}
