<?php

namespace CoolRunner\Utils\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Traits\Macroable;
use stdClass;

class UtilsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'utils');
        $this->mergeConfigFrom(__DIR__ . '/../config/audit.php', 'audit');
        $this->registerProviders();
        $this->registerAliases();
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('cr-utils.php'),
            ], 'config');
        }

        $this->registerMiddlewares();
        $this->registerConnection();
        $this->registerMixins();
    }

    protected function registerProviders()
    {
        $this->app->register(GuzzleClientProvider::class);
    }

    protected function registerAliases()
    {
        foreach (config('utils.aliases') as $alias => $binding) {
            AliasLoader::getInstance()->alias($alias, $binding);
        }
    }

    protected function registerMiddlewares()
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);
        foreach (config('utils.middleware') as $name => $class) {
            $router->aliasMiddleware($name, $class);
        }
    }

    protected function registerConnection()
    {
        foreach (config('utils.connections', []) as $connection => $setup) {
            config(["database.connections.$connection" => $setup]);
        }
    }

    protected function registerMixins()
    {
        /**
         * @var Macroable $macroable
         * @var stdClass $mixins
         */
        foreach (config('utils.mixins') as $macroable => $mixins) {
            foreach ($mixins as $mixin) {
                $macroable::mixin(new $mixin);
            }
        }
    }
}
