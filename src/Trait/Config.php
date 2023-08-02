<?php

namespace ThemeLooks\SecureLooks\Trait;

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
    }
}
