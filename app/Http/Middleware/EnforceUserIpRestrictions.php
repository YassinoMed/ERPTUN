<?php

namespace App\Http\Middleware;

use App\Services\Core\SecurityAccessService;
use Closure;
use Illuminate\Http\Request;

class EnforceUserIpRestrictions
{
    public function __construct(
        private readonly SecurityAccessService $security
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            if (! $this->security->isIpAllowed($request->user())) {
                $this->security->logSensitiveAccess('blocked_ip', get_class($request->user()), $request->user()->id, [
                    'reason' => 'Active IP restriction matched',
                ], $request->user(), $request);

                abort(403, 'Access denied from this IP address.');
            }

            $this->security->touchSession($request->user(), $request);
        }

        return $next($request);
    }
}
