<?php
namespace CoolRunner\Utils\Providers;

use Composer\InstalledVersions;
use CoolRunner\Utils\Http\Middleware\InputLogger;
use CoolRunner\Utils\Models\InLog;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use OwenIt\Auditing\AuditingServiceProvider;

class UtilsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'utils');
        $this->mergeConfigFrom(__DIR__ . '/../config/audit.php', 'audit');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('cr-utils.php'),
            ], 'config');
        }

        // Register router middlewares
        $router = $this->app->make(Router::class);
        $this->registerConnection();

    }

    protected function registerConnection() {
        config(['database.connections.logging' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => 'logging',
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
        ]]);
    }
}
