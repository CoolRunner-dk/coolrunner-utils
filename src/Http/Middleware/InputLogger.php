<?php


namespace CoolRunner\Utils\Http\Middleware;

use CoolRunner\Utils\Models\InLog;
use Illuminate\Http\Request;

class InputLogger
{
    protected static $time = null;

    /** @var InLog */
    protected static $log = null;

    public function handle(Request $request, \Closure $next)
    {
        if ($this->isExcluded($request)) {
            return $next($request);
        }

        if (!static::$log) {
            static::$time = microtime(true);
            try {
                static::$log = InLog::create($request);
            } catch (\Throwable $exception) {
                dd($exception);
                report($exception);
            }
        }

        $result = $next($request);


        if (static::$log && !static::$log->response) {
            $end_time = round((microtime(true) - static::$time) * 1000);
            try {
                static::$log->setResponse($result, $end_time);
            } catch (\Throwable $exception) {
                dd($exception);
                report($exception);
            }
        }


        if (static::$log && method_exists($result, 'header')) {
            $result->header('X-Input-Id', static::$log->uuid);
        }

        return $result;
    }

    public function isExcluded(Request $request)
    {
        if (strpos($request->path(), '__clockwork') === 0) {
            return true;
        }
    }

    public static function getLogId()
    {
        return static::$log->uuid ?? null;
    }
}