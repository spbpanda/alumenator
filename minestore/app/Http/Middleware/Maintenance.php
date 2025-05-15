<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class Maintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $settings = Setting::first();
        $maintenanceMode = $settings->is_maintenance;

        if ($maintenanceMode) {
            $allowedIPs = json_decode($settings->maintenance_ips, true);

            if (!in_array($request->ip(), array_column($allowedIPs, 'value'))) {
                abort(403, 'Our website is currently undergoing scheduled maintenance.');
            }
        }

        return $next($request);
    }
}
