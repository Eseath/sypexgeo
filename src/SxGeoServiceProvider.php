<?php
namespace Eseath\SxGeo;

use Illuminate\Support\ServiceProvider;
use Eseath\SxGeo\Commands\SxGeoUpdate;

class SxGeoServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/sxgeo.php' => config_path('sxgeo.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                SxGeoUpdate::class,
            ]);
        }
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sxgeo.php', 'sxgeo');

        $this->app->singleton(SxGeo::class, function ($app) {
            return new SxGeo($app['config']['sxgeo']['localPath']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sxgeo'];
    }
}
