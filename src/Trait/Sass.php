<?php

namespace ThemeLooks\SecureLooks\Trait;

use Closure;
use Illuminate\Http\Request;

class Sass
{
    use SecureLooksTrait, Helper;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (env(implode('', ['I', 'S_', 'US', 'ER', '_R', 'EGI', 'ST', 'ERE', 'D'])) == 1 && env(implode('', ['L', 'ICE', 'N', 'S', 'E_C', 'H', 'EC', 'KE', 'D'])) == 1) {
            $identifiers = $this->getKeys();
            foreach ($identifiers as $identifier) {
                if (!cache()->has('license-valid-' . $identifier->license_key)) {
                    $this->registerDomain($identifier->license_key, $identifier->item);
                }

                if (cache()->has('license-valid-' . $identifier->license_key) && !cache()->get('license-valid-' . $identifier->license_key)) {
                    $this->registerDomain($identifier->license_key, $identifier->item);
                }
            }
        }

        return $next($request);
    }
}
