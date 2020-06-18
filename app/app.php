<?php
use app\commands;
use app\spider;
use core\store\other;
return [
    'console' => [
        'meta' => [
            'name' => 'SQspider App',
            'version' => '1.0.0'
        ],
        'commands' => [
            'start' => commands\Start::class
        ]
    ],
    'store' => other\StoreWithObj::class,
    // 'store' => other\StoreWithRedis::class,
    'sortSpider' => [
        spider\test::class,
    ],
    'redis' => [
        'host' => '172.16.1.40',
        'port' => '6379',
        'prefix'  => 'php:spider:'
    ],
];