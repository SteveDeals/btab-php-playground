<?php

namespace Btab;

use Illuminate\Support\ServiceProvider;

class BtabServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/btab.php', 'btab');
    }

    public function boot()
    {
        // Publish assets
        $this->publishes([
            __DIR__ . '/../config/btab.php' => config_path('btab.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'btab');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
