<?php
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
                    'charset'  => 'utf8',
                    'host'     => '127.0.0.1',
                    'port'     => '8889',
                    'user'     => 'root',
                    'password' => 'root',
                    'dbname'   => 'ecamp3dev',
                ]
            ],
        ],
    ],
];
