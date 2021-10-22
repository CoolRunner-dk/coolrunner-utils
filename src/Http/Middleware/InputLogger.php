<?php


namespace CoolRunner\Utils\Http\Middleware;

use CoolRunner\Utils\Interfaces\Logging\Loggable;
use CoolRunner\Utils\Models\Logging\InputLog;
use CoolRunner\Utils\Support\Internal\SessionUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class InputLogger
{
    protected static ?InputLog $log = null;


    public static function getSessionUuid() {
        return static::getLog()?->session_uuid;
    }

    public function handle(Request $request, $next)
    {
        $request = $this->start($request);

        $response = $next($request);

        return $this->finish($response);
    }

    public static function getLog() : ?InputLog
    {
        return static::$log;
    }

    protected function start(Request $request) : Request
    {
        $route = ltrim($request->getPathInfo(), '/');

        foreach (static::getBlockedPrefixes() as $prefix) {
            if (\Illuminate\Support\Str::startsWith($route, $prefix) || $route == $prefix) {
                return $request;
            }
        }

        static::$log = new InputLog();

        $log               = static::getLog();
        $log->requested_at = now();

        $log->fillFromRequest($request);

        return $request;
    }

    protected function finish(Response $response) : Response
    {
        $log = static::getLog();

        if ($log) {
            try {
                $route = app('router')->current();
                $log   = $log->fillFromResponse($response)
                             ->fillFromRoute($route);

                $log->save();

                $controller = $route->controller;
                if ($controller instanceof Loggable) {
                    $controller->log($log);
                }
            }catch (\Throwable $e) {
                report($e);
            }
        }

        return $response;
    }

    public static function getBlockedPrefixes() {
        return Config::get('utils.drivers.input_log.blocked_prefixes',[]);
    }
}
