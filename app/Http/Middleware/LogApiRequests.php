<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $payload = $request->except(['password', 'password_confirmation']);
        $responsePayload = null;

        if (method_exists($response, 'getContent')) {
            $content = $response->getContent();
            $decoded = json_decode((string) $content, true);
            $responsePayload = is_array($decoded) ? $decoded : ['content' => mb_substr((string) $content, 0, 2000)];
        }

        ApiLog::create([
            'api_client_id' => optional($request->attributes->get('api_client'))->id,
            'user_id' => $request->user()?->id,
            'route' => optional($request->route())->uri() ?? $request->path(),
            'method' => $request->method(),
            'status_code' => method_exists($response, 'status') ? $response->status() : null,
            'request_payload' => $payload,
            'response_payload' => $responsePayload,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 65535),
            'requested_at' => now(),
        ]);

        return $response;
    }
}
