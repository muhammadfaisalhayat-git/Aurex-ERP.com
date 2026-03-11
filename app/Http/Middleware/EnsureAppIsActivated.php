<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAppIsActivated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware for the activation routes themselves
        if ($request->routeIs('activation.*')) {
            return $next($request);
        }

        $systemSettings = \App\Models\SystemSetting::withoutGlobalScope('tenant')
            ->whereIn('key', ['master_server_address', 'deployment_url'])
            ->pluck('value', 'key');

        $isActivated = !empty($systemSettings['master_server_address']) && !empty($systemSettings['deployment_url']);

        if (!$isActivated) {
            return redirect()->route('activation.index');
        }

        return $next($request);
    }
}
