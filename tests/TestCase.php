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
            "database.connections.advisering" => config('database.connections.testing')
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
