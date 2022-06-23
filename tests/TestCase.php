<?php

namespace CoolRunner\Utils\Tests;

use CoolRunner\Utils\Providers\GuzzleClientProvider;
use CoolRunner\Utils\Providers\UtilsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup


        config([
            "database.connections.advisering" => array_merge(config('database.connections.advisering'), [
                "host" => "mariadb",
                "username" => "root",
                "password" => "root",
            ])
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            GuzzleClientProvider::class,
            UtilsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
