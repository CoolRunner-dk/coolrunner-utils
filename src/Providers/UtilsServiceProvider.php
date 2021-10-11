<?php
namespace CoolRunner\Utils\Providers;

use CoolRunner\Utils\Http\Middleware\InputLogger;
use CoolRunner\Utils\Models\InLog;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class UtilsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'utils');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/config.php' => config_path('CoolRunnerUtils.php'),
            ], 'config');
        }

        // Register router middlewares
        $router = $this->app->make(Router::class);
    }
}
