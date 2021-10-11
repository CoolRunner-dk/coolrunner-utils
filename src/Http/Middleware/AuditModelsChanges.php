<?php


namespace CoolRunner\Utils\Http\Middleware;

use Closure;

class AuditModelsChanges
{

    public static $auditing_enabled = false;

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
}
