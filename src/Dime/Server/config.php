<?php

return [
    'displayErrorDetails' => true,
    'routes' => [
        'apidoc' => [
            'route' => '/apidoc[/{resource}]',
            'endpoint' => 'Dime\Server\Endpoint\Apidoc',
            'map' => ['GET']
        ],
        'resource_get' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceGet',
            'map' => ['GET'],
            'middleware' => [
                'Dime\Server\Middleware\ResourceType'
            ]
        ],
        'resource_list' => [
            'route' => '/api/{resource}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceList',
            'map' => ['GET'],
            'middleware' => [
                'Dime\Server\Middleware\ResourceType'
            ]
        ],
        'resource_post' => [
            'route' => '/api/{resource}',
            'endpoint' => 'Dime\Server\Endpoint\ResourcePost',
            'map' => ['POST'],
            'middleware' => [
                'Dime\Server\Middleware\ResourceType'
            ]
        ],
        'resource_put' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourcePut',
            'map' => ['PUT'],
            'middleware' => [
                'Dime\Server\Middleware\ResourceType'
            ]
        ],
        'resource_delete' => [
            'route' => '/api/{resource}/{id:\d+}',
            'endpoint' => 'Dime\Server\Endpoint\ResourceDelete',
            'map' => ['DELETE'],
            'middleware' => [
                'Dime\Server\Middleware\ResourceType'
            ]
        ]
    ]
];