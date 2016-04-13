<?php

namespace Dime\Server;

return [
    'api' => [
        'version' => 1,
        'headers' => [
            'Content-Type' => 'application/json; charset=utf-8',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods' => 'GET,POST,PUT,DELETE'
        ],
        'resources' => [],
    ],
    'displayErrorDetails' => true,
    'doctrine' => [
        'connection' => [
            'dbname' => 'dime',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'mysqli',
            'charset' => 'utf-8'
        ],
        'auto_generate_proxies' => 2,
        'annotation_paths' => [
            ROOT_DIR . '/src/Dime/Server/Entity/',
            ROOT_DIR . '/src/Dime/Security/Entity/',
            ROOT_DIR . '/src/Dime/Api/Entity/'
        ],
    ],
    'migrations' => [
        'namespace' => 'Dime\Server\Migrations',
        'directory' => __DIR__ . '/Migrations',
        'table_name' => 'migration_versions',
    ],
    'routes' => [
        'resource_get' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceGet',
            'map' => ['GET'],
            'middleware' => []
        ],
        'resource_list' => [
            'route' => '/api/{resource}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceList',
            'map' => ['GET'],
            'middleware' => []
        ],
        'resource_post' => [
            'route' => '/api/{resource}',
            'endpoint' => 'Dime\Server\Endpoint\ResourcePost',
            'map' => ['POST'],
            'middleware' => []
        ],
        'resource_put' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourcePut',
            'map' => ['PUT'],
            'middleware' => []
        ],
        'resource_delete' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceDelete',
            'map' => ['DELETE'],
            'middleware' => []
        ]
    ]
];
