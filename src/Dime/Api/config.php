<?php

namespace Dime\Api;



return [
    'api' => [
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
    ],

    'migrations' => [
        'namespace' => 'Dime\Api\Migrations',
        'directory' => __DIR__ . '/Migrations',
        'table_name' => 'migration_versions',
    ]
];
