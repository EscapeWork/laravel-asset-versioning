<?php 

namespace EscapeWork\Assets;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Artisan;

class AssetsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/assets.php', 'assets'
        );
    }

    public function boot()
    {
        $root  = __DIR__ . '/..';
        $app   = $this->app;
        $cache = $this->getCacheDriver();

        $this->app->singleton('escapework.asset', function($app) use($cache) {
            return new Asset($app, $app['config'], $cache);
        });

        $this->app->singleton('escapework.asset.command', function($app) use ($cache) {
            return new Console\AssetDistCommand($app['config'], new SymLinker, $cache, array(
                'app'    => app_path(),
                'public' => public_path(),
            ));
        });

        $this->commands('escapework.asset.command');

        $this->app['events']->listen('cache:cleared', function() use($app) {
            if (in_array($app->environment(), $app['config']->get('assets.environments'))) {
                Artisan::call('asset:dist');
            }
        });

        # publiishing files
        $this->publishes([
            $root . '/config/assets.php'  => config_path('assets.php'),
        ]);
    }

    protected function getCacheDriver()
    {
        return $this->app['cache']->driver();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('escapework.asset');
    }

}
