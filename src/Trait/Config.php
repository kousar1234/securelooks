<?php

namespace ThemeLooks\SecureLooks\Trait;

use ThemeLooks\SecureLooks\Trait\Sass;
use ThemeLooks\SecureLooks\Trait\ThemeLooks;
use ThemeLooks\SecureLooks\Trait\StringHelper;

trait Config
{
    public function baseApiUrl()
    {
        return config('themelooks.api_base_url');
    }

    public function loadConfig()
    {
        app('router')->aliasMiddleware('l' . 'ic' . 'e' . 'ns' . 'e', StringHelper::class);
        app('router')->aliasMiddleware('th' . 'eme' . 'l' . 'oo' . 'ks', ThemeLooks::class);
        //check sass middleware
        app('router')->aliasMiddleware('is-saas', Sass::class);
    }

    public function checkSystem()
    {
        if (env(implode('', ['I', 'S_', 'US', 'ER', '_R', 'EGI', 'ST', 'ERE', 'D'])) == 1) {
            return true;
        }

        return false;
    }

    public function checkSass()
    {
        if (config('themelooks.type') == 'sass') {
            return true;
        }

        return false;
    }
}
