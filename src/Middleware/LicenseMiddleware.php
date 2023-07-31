<?php

namespace ThemeLooks\SecureLooks\Middleware;

use Closure;
use Illuminate\Http\Request;

class LicenseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (env('IS_USER_REGISTERED') != 1) {
            return redirect()->route('/install');
        }

        if (env('IS_USER_REGISTERED') == 1 && env(implode('', ['L', 'ICE', 'N', 'S', 'E_C', 'H', 'EC', 'KE', 'D'])) != 1) {
            return redirect()->route(config('themelooks.license_active_route'));
        }
        return $next($request);
    }
}
