<?php namespace EscapeWork\Assets;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

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
        $this->package('escapework/laravel-asset-versioning', 'laravel-asset-versioning', realpath(__DIR__ . '/../..'));
    }
    
    public function boot()
    {
        $app   = $this->app;
        $cache = $this->getCacheDriver();

        $this->app['escapework.asset'] = $this->app->share(function($app) use($cache)
        {
            return new Asset($app, $app['config'], $cache);
        });

        $this->app['escapework.asset.command'] = $this->app->share(function($app) use ($cache)
        {
            return new Commands\AssetDistCommand($app['config'], $app['files'], $cache, array(
                'app'    => app_path(),
                'public' => public_path(),
            ));
        });

        $this->commands('escapework.asset.command');

        $this->app->booting(function()
        {
            $loader = AliasLoader::getInstance();

            $loader->alias('Asset', 'EscapeWork\Assets\Facades\Asset');
        });

        $this->app['events']->listen('cache:cleared', function() use($app)
        {
            $app['artisan']->call('asset:dist');
        });
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
