<?php

return [
    'settings' => [
        'auth' => [
            'realm'   => 'DimeTimetracker',
            'user'    => 'Dime\Server\Entity\User',
            'access'  => 'Dime\Server\Entity\Access',
            'expires' => '1 hour'
        ],
        
        'displayErrorDetails' => true,
        
        'doctrine' => [
            'connection' => [
                'dbname' => 'Dime',
                'user' => 'root',
                'password' => '',
                'host' => 'localhost',
                'driver' => 'mysqli',
                'charset' => 'utf-8'
            ],
            'auto_generate_proxies' => true,
            'annotation_paths' => [
                ROOT_DIR . '/src/Dime/Server/Model/',
                ROOT_DIR . '/src/Dime/Api/Model/'
            ],
        ],
        
        'api' => [
            'version' => 1,
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Allow-Methods' => 'GET,POST,PUT,DELETE'
            ],
            'resources' => [
                'activity' => [
                    'entity' => 'Dime\Api\Entity\Activity'
                ],
                'customer' => [
                    'entity' => 'Dime\Api\Entity\Customer'
                ],
                'project' => [
                    'entity' => 'Dime\Api\Entity\Project'
                ],
                'service' => [
                    'entity' => 'Dime\Api\Entity\Service'
                ],
                'setting' => [
                    'entity' => 'Dime\Api\Entity\Setting'
                ],
                'tag' => [
                    'entity' => 'Dime\Api\Entity\Tag'
                ],
                'timeslice' => [
                    'entity' => 'Dime\Api\Entity\Timeslice'
                ],
            ]
        ]
    ]
];
