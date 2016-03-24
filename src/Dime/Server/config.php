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
            'middleware' => [
                'Dime\Server\Middleware\ContentTransformer:run',
                'Dime\Server\Middleware\ContentNegotiation:run',
                'Dime\Server\Middleware\ResourceType:run',
//            'Dime\Server\Middleware\Authorization:run',
            ]
        ],
        'resource_list' => [
            'route' => '/api/{resource}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceList',
            'map' => ['GET'],
            'middleware' => [
                'Dime\Server\Middleware\ContentTransformer:run',
                'Dime\Server\Middleware\ContentNegotiation:run',
                'Dime\Server\Middleware\ResourceType:run',
            ]
        ],
        'resource_post' => [
            'route' => '/api/{resource}',
            'endpoint' => 'Dime\Server\Endpoint\ResourcePost',
            'map' => ['POST'],
            'middleware' => [
                'Dime\Server\Middleware\Validation:run',
                'Dime\Api\Middleware\Assign:run',
                'Dime\Server\Middleware\ContentTransformer:run',
                'Dime\Server\Middleware\ContentNegotiation:run',
                'Dime\Server\Middleware\ResourceType:run',
            ]
        ],
        'resource_put' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourcePut',
            'map' => ['PUT'],
            'middleware' => [
                'Dime\Server\Middleware\Validation:run',
                'Dime\Api\Middleware\Assign:run',
                'Dime\Server\Middleware\ContentTransformer:run',
                'Dime\Server\Middleware\ContentNegotiation:run',
                'Dime\Server\Middleware\ResourceType:run',
            ]
        ],
        'resource_delete' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceDelete',
            'map' => ['DELETE'],
            'middleware' => [
                'Dime\Server\Middleware\ContentTransformer:run',
                'Dime\Server\Middleware\ContentNegotiation:run',
                'Dime\Server\Middleware\ResourceType:run',
            ]
        ]
    ]
];
