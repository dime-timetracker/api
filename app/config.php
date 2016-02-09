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
            'annotation_files' => [
                ROOT_DIR . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
            ],
            'annotation_namespaces' => [
                'JMS\Serializer\Annotation' => ROOT_DIR . '/vendor/jms/serializer/src'  
            ],
            'annotation_paths' => [
                ROOT_DIR . '/src/Dime/Server/Model/'
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
                    'entity' => 'Dime\Server\Entity\Activity'
                ],
                'customer' => [
                    'entity' => 'Dime\Server\Entity\Customer'
                ],
                'project' => [
                    'entity' => 'Dime\Server\Entity\Project'
                ],
                'service' => [
                    'entity' => 'Dime\Server\Entity\Service'
                ],
                'setting' => [
                    'entity' => 'Dime\Server\Entity\Setting'
                ],
                'tag' => [
                    'entity' => 'Dime\Server\Entity\Tag'
                ],
                'timeslice' => [
                    'entity' => 'Dime\Server\Entity\Timeslice'
                ],
            ]
        ]
    ]
];
