<?php

namespace ThemeLooks\SecureLooks;

use Illuminate\Support\ServiceProvider;
use ThemeLooks\SecureLooks\SecureLooksService;
use SecureLooks;

class SecureLooksServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('secure-looks', function () {
            return new SecureLooksService;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        SecureLooks::init();
    }
}
