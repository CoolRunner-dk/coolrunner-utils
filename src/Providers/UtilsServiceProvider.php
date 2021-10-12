<?php
namespace CoolRunner\Utils\Providers;

use Composer\InstalledVersions;
use CoolRunner\Utils\Http\Middleware\AuditModelsChanges;
use CoolRunner\Utils\Http\Middleware\InputLogger;
use CoolRunner\Utils\Models\InLog;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use OwenIt\Auditing\AuditingServiceProvider;

class UtilsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'utils');
        $this->mergeConfigFrom(__DIR__ . '/../config/audit.php', 'audit');
        $this->registerProviders();
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






    }

    protected function registerMiddlewares() {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('audit', AuditModelsChanges::class);
        $router->aliasMiddleware('input_log', InputLogger::class);

    }

    protected function registerConnection() {
        foreach (config('utils.connections', []) as $connection => $setup) {
            config(["database.connections.$connection" => $setup]);
        }
    }

    protected function registerProviders() {
        $this->app->register(GuzzleClientProvider::class);
    }
}
