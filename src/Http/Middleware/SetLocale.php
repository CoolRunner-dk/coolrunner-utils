<?php

namespace CoolRunner\Utils\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = config('app.fallback_locale');

        if (Session::has('locale')) {
            $locale = Session::get('locale', Config::get('app.locale'));
        } elseif (strlen($request->server('HTTP_ACCEPT_LANGUAGE'))) {
            $locale = $this->getPreferredLocale($request);
        }

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return string|null
     * @author Mikkel Nørgaard
     */
    public function getPreferredLocale(Request $request): ?string
    {
        return $request->getPreferredLanguage($this->getPreferredLocales());
    }

    /**
     * @return array
     * @author Mikkel Nørgaard
     */
    public function getPreferredLocales(): array
    {
        return array_map('basename', File::directories(resource_path('lang')));
    }
}