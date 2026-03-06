<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class ServiceRoutingGuard
{
    public function handle(Request $request, Closure $next)
    {
        $mode = env('SERVICE_MODE');
        if (!$mode) {
            return $next($request);
        }
        $allowed = env('SERVICE_ALLOWED_PREFIXES', '');
        $allowedList = array_values(array_filter(array_map('trim', explode(',', $allowed))));
        if (empty($allowedList)) {
            abort(404);
        }
        $path = ltrim($request->path(), '/');
        foreach ($allowedList as $prefix) {
            $prefix = trim($prefix, '/');
            if ($prefix === '') {
                continue;
            }
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $next($request);
            }
        }
        abort(404);
    }
}
