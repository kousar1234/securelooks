<?php

namespace ThemeLooks\SecureLooks\Trait;

use Closure;
use Illuminate\Http\Request;
use ThemeLooks\SecureLooks\Model\License;

class ThemeLooks
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
        $licenses = License::select(['license_key'])->get();
        if ($licenses->count() < 1) {
            return redirect()->route(config('themelooks.license_verify_route'));
        }
        return $next($request);
    }
}
