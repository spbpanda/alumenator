<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TrackVisitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->hasVisit($request->ip())) {
            SiteVisit::query()
                ->firstOrNew(['created_at' => today()])
                ->visit();
        }
        return $next($request);
    }

    private function hasVisit(string $ipAddress): bool
    {
        $time = now()->endOfDay()->diffInMinutes(now());
        $visitIps = [];

        if (cache()->has('visit_ips')) {
            $visitIps = cache()->get('visit_ips');
        }

        if (in_array($ipAddress, $visitIps)) {
            return true;
        }

        $visitIps[] = $ipAddress;
        cache()->put('visit_ips', $visitIps, $time);

        return false;
    }
}
