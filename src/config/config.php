<?php

return [
    'drivers' => [
        'client_log' => [
            'table' => null,
            'connection' => env('CLIENT_LOG_CONNECTION', 'logging')
        ],
        'input_log' => [
            'table' => null,
            'connection' => env('INPUT_LOG_CONNECTION', 'logging'),
            'blocked_prefixes' => [
                '__clockwork',
            ]
        ],
    ],
    "jobs" => [
        "save_model" => [
            "queue" => env('UTILS_JOBS_QUEUE'),
            "connection" => env('UTILS_JOBS_CONNECTION')
        ]
    ],
    'connections' => [
        'logging' => [
            'driver' => 'mysql',
            'url' => env('LOGGING_DATABASE_URL'),
            'host' => env('LOGGING_DB_HOST'),
            'port' => env('LOGGING_DB_PORT', '3306'),
            'database' => env('LOGGING_DB_DATABASE', 'logging'),
            'username' => env('LOGGING_DB_USERNAME', ''),
            'password' => env('LOGGING_DB_PASSWORD', ''),
        ],
        'advisering' => [
            'driver' => 'mysql',
            'url' => env('ADVISERING_DATABASE_URL'),
            'host' => env('ADVISERING_DB_HOST'),
            'port' => env('ADVISERING_DB_PORT', '3306'),
            'database' => env('ADVISERING_DB_DATABASE', 'advisering'),
            'username' => env('ADVISERING_DB_USERNAME', ''),
            'password' => env('ADVISERING_DB_PASSWORD', ''),
        ]
    ],
    "guzzle" => [
        'default_client' => 'default',
        'default_config' => [
            \GuzzleHttp\RequestOptions::TIMEOUT => 5,
            \GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => 1,
            \GuzzleHttp\RequestOptions::HTTP_ERRORS => true,
            \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => true,
        ],

        'clients' => [
            'default' => [],
        ],
    ],
    'mixins' => [
        \Illuminate\Support\Arr::class => [
            \CoolRunner\Utils\Mixins\Arr::class,
        ],

        \Illuminate\Support\Carbon::class => [
            \CoolRunner\Utils\Mixins\Dates::class,
        ],

        \Illuminate\Support\Str::class => [
            \CoolRunner\Utils\Mixins\Str::class,
        ],

        \Illuminate\Support\Facades\Auth::class => [
            \CoolRunner\Utils\Mixins\Auth::class,
        ],

        \Illuminate\Database\Query\Builder::class => [
            \CoolRunner\Utils\Mixins\Builder::class,
        ],

        \Illuminate\Database\Eloquent\Builder::class => [
            \CoolRunner\Utils\Mixins\Eloquent::class,
        ],
    ],
    'aliases' => [
        "Num"       => \CoolRunner\Utils\Support\Tools\Number::class,
        "Bytes"     => \CoolRunner\Utils\Support\Tools\Bytes::class,
        "Converter" => \CoolRunner\Utils\Support\Tools\Converter::class,
        "Coords"    => \CoolRunner\Utils\Support\Tools\Coords::class,
        "Advisering" => \CoolRunner\Utils\Support\Tools\Advisering::class,
    ],
    'middleware' => [
        'audit'     => CoolRunner\Utils\Http\Middleware\AuditModelsChanges::class,
        'input_log' => CoolRunner\Utils\Http\Middleware\InputLogger::class,
        'locale'    => \CoolRunner\Utils\Http\Middleware\SetLocale::class
    ],
];
