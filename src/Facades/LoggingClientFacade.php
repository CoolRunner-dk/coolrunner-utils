<?php

namespace CoolRunner\Utils\Facades;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Facade;

class LoggingClientFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "LoggedClient";
    }


    public static function make(array $parameters = []) : Client {
        return app('LoggedClient', $parameters);
    }
}
