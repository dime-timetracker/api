<?php

return [
    'settings' => [
        'auth' => [
            'realm'   => 'DimeTimetracker',
            'user'    => 'Dime\Server\Entity\User',
            'access'  => 'Dime\Server\Entity\Access',
            'expires' => '1 hour',
            'authorizeType' => 'Dime\Server\Entity\Login',
        ],
        
        'displayErrorDetails' => true,
        
        'doctrine' => [
            'connection' => [
                'dbname' => 'dime_dev',
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
            'table_name' => 'migration_versions',
            'migrations_directory' => ROOT_DIR . '/app/DoctrineMigrations',
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
                    'entity' => 'Dime\Api\Entity\Activity',
                    'pageSize' => 100
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
                    'entity' => 'Dime\Api\Entity\Timeslice',
                    'pageSize' => 100
                ],
            ]
        ]
    ]
];
