<?php

return [
    'drivers' => [
        'client_log' => [
            'table' => null,
            'connection' => env('LOGGING_DB_DATABASE', 'logging')
        ],
        'input_log' => [
            'table' => null,
            'connection' => env('LOGGING_DB_DATABASE', 'logging')
        ],
        
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
    ]
];
