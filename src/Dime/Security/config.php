<?php

namespace Dime\Security;

return [
    'auth' => [
        'realm' => 'DimeTimetracker',
        'user' => 'Dime\Server\Entity\User',
        'access' => 'Dime\Server\Entity\Access',
        'expires' => '1 hour',
        'authorizeType' => 'Dime\Server\Entity\Login',
    ],
    'routes' => [
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
        ]
    ]
];
