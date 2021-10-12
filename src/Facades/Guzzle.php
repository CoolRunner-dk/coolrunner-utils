<?php


namespace CoolRunner\Utils\Facades;


use CoolRunner\Utils\Managers\GuzzleManager;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Facade;

/**
 * Class Guzzle
 *
 * @package CoolRunner\Utils\Facades
 * @mixin GuzzleManager
 */
class Guzzle extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GuzzleManager::class;
    }
}
