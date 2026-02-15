<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Set default company if not set in session
            if (!session()->has('active_company_id')) {
                session(['active_company_id' => $user->company_id]);
            }

            // Set default branch if not set in session
            if (!session()->has('active_branch_id')) {
                session(['active_branch_id' => $user->branch_id]);
            }

            // Super Admin can switch companies via request
            if ($user->hasRole('Super Admin') && $request->has('switch_company_id')) {
                session(['active_company_id' => $request->get('switch_company_id')]);
                // Clear branch when switching company to avoid mismatch
                session()->forget('active_branch_id');
            }

            // Company Admin can switch branches via request
            if ($user->hasRole('Company Admin') && $request->has('switch_branch_id')) {
                session(['active_branch_id' => $request->get('switch_branch_id')]);
            }
        }

        return $next($request);
    }
}
