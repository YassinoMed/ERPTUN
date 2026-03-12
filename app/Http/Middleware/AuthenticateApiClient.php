<?php

namespace App\Http\Middleware;

use App\Services\Core\ApiClientService;
use Closure;
use Illuminate\Http\Request;

class AuthenticateApiClient
{
    public function __construct(
        private readonly ApiClientService $apiClientService
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-Api-Client');
        $secret = $request->header('X-Api-Secret');

        if (blank($key) || blank($secret)) {
            return response()->json(['message' => 'Missing API client credentials.'], 401);
        }

        $client = $this->apiClientService->validate($key, $secret);
        if (! $client) {
            return response()->json(['message' => 'Invalid API client credentials.'], 401);
        }

        $request->attributes->set('api_client', $client);

        return $next($request);
    }
}
