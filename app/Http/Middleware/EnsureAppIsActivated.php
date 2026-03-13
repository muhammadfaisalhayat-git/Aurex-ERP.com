<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // Skip middleware for activation, login, and language routes
        if ($request->routeIs('activation.*', 'login', 'logout', 'language.switch')) {
            return $next($request);
        }

        // Skip for landing routes only on port 8080
        if ($request->routeIs('landing.*') && $request->getPort() == 8080) {
            return $next($request);
        }

        // Skip for assets
        if ($request->is('css/*', 'images/*', 'js/*', 'fonts/*', 'vendor/*')) {
            return $next($request);
        }

        $systemSettings = \App\Models\SystemSetting::withoutGlobalScope('tenant')
            ->whereIn('key', ['master_server_address', 'deployment_url'])
            ->pluck('value', 'key');

        $isActivated = !empty($systemSettings['master_server_address']) && !empty($systemSettings['deployment_url']);

        if (!$isActivated) {
            return redirect()->route('activation.index');
        }

        // Check if this specific deployment is registered
        $currentUrl = rtrim(strtolower($request->root()), '/');

        // 1. Master Server Bypass: Always allow the Master Server to access itself
        $masterUrl = rtrim(strtolower($systemSettings['master_server_address'] ?? ''), '/');
        if ($currentUrl === $masterUrl) {
            return $next($request);
        }

        // 2. Deployment Authorization: Require manual registration for any other instance
        try {
            $deploymentExists = \App\Models\Deployment::where(DB::raw("LOWER(RTRIM(url, '/'))"), $currentUrl)->exists();
        } catch (\Exception $e) {
            $deploymentExists = false;
        }

        if (!$deploymentExists) {
            return redirect()->route('activation.index')->with('error', 'This deployment instance (' . $currentUrl . ') has not been authorized. Please contact your Super Administrator on the Master Server (' . $masterUrl . ') to register this URL.');
        }

        return $next($request);
    }
}
