<?php

namespace Jcove\Promotion;

use Illuminate\Support\ServiceProvider;

class PromotionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (file_exists($routes = __DIR__.'/routes.php')) {
            $this->loadRoutesFrom($routes);
        }
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()],'laravel-promotion-config');
            $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang')], 'laravel-promotion-lang');
            $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'laravel-promotion-migrations');

        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('promotion', function ($app) {
            return new PromotionMain($app['session'], $app['config']);
        });
    }
}
