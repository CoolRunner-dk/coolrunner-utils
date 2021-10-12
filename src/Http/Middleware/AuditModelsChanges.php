<?php


namespace CoolRunner\Utils\Http\Middleware;

use Closure;

class AuditModelsChanges
{

    protected static bool $auditing_enabled = false;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        static::$auditing_enabled = true;
        return $next($request);
    }

    public static function isEnabled() : bool
    {
        return static::$auditing_enabled;
    }
}
