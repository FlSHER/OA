<?php

declare(strict_types=1);

namespace Fisher\Amap\Providers;

use App\Support\PackageHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Boorstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        // Register a database migration path.
        $this->loadMigrationsFrom($this->app->make('path.amap.migrations'));

        // Register translations.
        $this->loadTranslationsFrom($this->app->make('path.amap.lang'), 'amap');

        // Register view namespace.
        $this->loadViewsFrom($this->app->make('path.amap.views'), 'amap');

        // Publish public resource.
        $this->publishes([
            $this->app->make('path.amap.assets') => $this->app->publicPath().'/assets/amap',
        ], 'amap-public');

        // Publish config.
        $this->publishes([
            $this->app->make('path.amap.config').'/amap.php' => $this->app->configPath('amap.php'),
        ], 'amap-config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Bind all of the package paths in the container.
        $this->bindPathsInContainer();

        // Merge config.
        $this->mergeConfigFrom(
            $this->app->make('path.amap.config').'/amap.php',
            'amap'
        );

        // register cntainer aliases
        $this->registerCoreContainerAliases();

        // Register singletons.
        $this->registerSingletions();

        // Register package handlers.
        $this->registerPackageHandlers();
    }

    /**
     * Bind paths in container.
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        foreach ([
            'path.amap' => $root = dirname(dirname(__DIR__)),
            'path.amap.assets' => $root.'/assets',
            'path.amap.config' => $root.'/config',
            'path.amap.database' => $database = $root.'/database',
            'path.amap.resources' => $resources = $root.'/resources',
            'path.amap.lang' => $resources.'/lang',
            'path.amap.views' => $resources.'/views',
            'path.amap.migrations' => $database.'/migrations',
            'path.amap.seeds' => $database.'/seeds',
        ] as $abstract => $instance) {
            $this->app->instance($abstract, $instance);
        }
    }

    /**
     * Register singletons.
     *
     * @return void
     */
    protected function registerSingletions()
    {
        // Owner handler.
        $this->app->singleton('amap:handler', function () {
            return new \Fisher\Amap\Handlers\PackageHandler();
        });

        // Develop handler.
        $this->app->singleton('amap:dev-handler', function ($app) {
            return new \Fisher\Amap\Handlers\DevPackageHandler($app);
        });
    }

    /**
     * Register the package class aliases in the container.
     *
     * @return void
     */
    protected function registerCoreContainerAliases()
    {
        foreach ([
            'amap:handler' => [
                \Fisher\Amap\Handlers\PackageHandler::class,
            ],
            'amap:dev-handler' => [
                \Fisher\Amap\Handlers\DevPackageHandler::class,
            ],
        ] as $abstract => $aliases) {
            foreach ($aliases as $alias) {
                $this->app->alias($abstract, $alias);
            }
        }
    }

    /**
     * Register package handlers.
     *
     * @return void
     */
    protected function registerPackageHandlers()
    {
        $this->loadHandleFrom('amap', 'amap:handler');
        $this->loadHandleFrom('amap-dev', 'amap:dev-handler');
    }

    /**
     * Register handler.
     *
     * @param string $name
     * @param \App\Support\PackageHandler|string $handler
     * @return void
     */
    private function loadHandleFrom(string $name, $handler)
    {
        PackageHandler::loadHandleFrom($name, $handler);
    }
}
