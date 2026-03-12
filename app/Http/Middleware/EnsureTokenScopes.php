<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenScopes
{
    public function handle(Request $request, Closure $next, string ...$requiredScopes): Response
    {
        $user = $request->user();
        $token = $user?->currentAccessToken();

        if (! $user || ! $token) {
            abort(401, 'Unauthenticated.');
        }

        foreach ($requiredScopes as $scope) {
            if (! $user->tokenCan($scope) && ! $user->tokenCan('*')) {
                abort(403, 'Insufficient scope.');
            }
        }

        $request->attributes->set('token_scopes', $requiredScopes);

        return $next($request);
    }
}
