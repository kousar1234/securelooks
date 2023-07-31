<?php

namespace ThemeLooks\SecureLooks;

use SecureLooks;
use Illuminate\Support\ServiceProvider;
use ThemeLooks\SecureLooks\SecureLooks as SecureLooksService;
use ThemeLooks\SecureLooks\Middleware\LicenseMiddleware;

class SecureLooksServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //Register secure looks  service
        $this->app->singleton('secure-looks', function () {
            return new SecureLooksService;
        });

        //Register middleware
        app('router')->aliasMiddleware('license', LicenseMiddleware::class);

        //Config
        $this->mergeConfigFrom(__DIR__ . '/../config/themelooks.php', 'themelooks');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Published config
        $this->publishes([
            __DIR__ . '/../config/themelooks.php' => config_path('themelooks.php'),
        ], 'config');

        SecureLooks::init();
    }
}
