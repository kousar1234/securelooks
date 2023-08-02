<?php

namespace ThemeLooks\SecureLooks\Trait;

use Closure;
use Illuminate\Http\Request;

class StringHelper
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
        if (env(implode('', ['I', 'S_', 'US', 'ER', '_R', 'EGI', 'ST', 'ERE', 'D'])) == 1 && env(implode('', ['L', 'ICE', 'N', 'S', 'E_C', 'H', 'EC', 'KE', 'D'])) != 1) {
            return redirect()->route(config('themelooks.license_active_route'));
        }
        return $next($request);
    }
}
