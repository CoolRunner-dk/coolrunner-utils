<?php


namespace CoolRunner\Utils\Http\Middleware;

use App\Interfaces\Http\Loggable;
use CoolRunner\Utils\Models\Logging\InputLog;
use Illuminate\Database\Eloquent\Model;
use Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InputLogger
{
    protected static ?InputLog $log = null;

    protected $blocked_prefixes = [
        '__clockwork',
        ''
    ];

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

        foreach ($this->blocked_prefixes as $prefix) {
            if (Str::startsWith($route, $prefix) || $route == $prefix) {
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
                $route = \Route::current();
                $log = $log->fillFromResponse($response)
                    ->fillFromRoute($route);

                $log->save();

                /** @var Loggable|\App\Http\Controllers\Controller $controller */
                if (class_implements(($controller = $route->controller), Loggable::class)) {
                    $controller->log($log);
                }
            }catch (\Throwable $e) {}
        }


        return $response;
    }
}
