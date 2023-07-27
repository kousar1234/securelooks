<?php

namespace ThemeLooks\SecureLooks;

use Illuminate\Support\Facades\Facade;

class SecureLooksFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'secure-looks';
    }
}
