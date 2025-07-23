<?php

namespace ThemeLooks\SecureLooks;

use AppLoader;
use Illuminate\Support\ServiceProvider;
use ThemeLooks\SecureLooks\SecureLooks as SecureLooksService;

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

        AppLoader::initApp();

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

        AppLoader::init();
    }
}
