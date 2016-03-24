<?php


return [
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
];
