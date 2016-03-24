<?php


return [
    'login' => [
        'route' => '/login',
        'endpoint' => 'Dime\Server\Endpoint\Authentication:login',
        'map' => ['POST'],
        'middleware' => [
            'Dime\Server\Middleware\Validation:run',
            'Dime\Server\Middleware\ContentTransformer:run',
            'Dime\Server\Middleware\ContentNegotiation:run',
            'Dime\Server\Middleware\AuthorizeType:run',
        ]
    ],
    'logout' => [
        'route' => '/logout',
        'endpoint' => 'Dime\Server\Endpoint\Authentication:logout',
        'map' => ['POST'],
        'middleware' => [
            'Dime\Server\Middleware\Authorization:run',
        ]
    ],
    'parser_analyse' => [
        'route' => '/analyse/{name}',
        'endpoint' => 'Dime\Parser\Endpoint\Parser:analyse',
        'map' => ['POST'],
        'middleware' => [
            'Dime\Parser\Middleware\ActivityDescription:run',
            'Dime\Parser\Middleware\ActivityRelation:run',
            'Dime\Parser\Middleware\Datetime:run',
            'Dime\Parser\Middleware\Duration:run',
            'Dime\Parser\Middleware\Timerange:run',
            'Dime\Server\Middleware\ContentTransformer:run',
            'Dime\Server\Middleware\ContentNegotiation:run',
        ]
    ],
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
];
