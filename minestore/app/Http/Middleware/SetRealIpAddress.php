<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SetRealIpAddress
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if ($request->hasHeader('X-Forwarded-For')) {
                $forwardedFor = $request->header('X-Forwarded-For');

                if (!empty($forwardedFor)) {
                    $ips = explode(',', $forwardedFor);
                    if (!empty($ips[0])) {
                        $ip = trim($ips[0]);
                        if (filter_var($ip, FILTER_VALIDATE_IP)) {
                            $request->server->set('REMOTE_ADDR', $ip);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('[Middleware] Error setting real IP address: ' . $e->getMessage());
        }

        return $next($request);
    }
}
