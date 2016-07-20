<?php

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 */
return [
    'redis_backend' => '127.0.0.1:6379',
    'redis_backend_db' => null, //redis 数据库名称
    'prefix' => null, //前缀 多个php-resque时候用于区分
    'queue' => [//监控的任务队列， 和监控的进程数 多个用','分开 '*'为监控所有队列
        ['high', 1],
//        ['high,normal',2],
//        ['high,normal,low', 3],
    ],
    'crontab' => [//定时任务
        '5' => ['Crontab'], //秒做单位
    ],
    'plugins' => [//插件注册
        'beforePerform' => [['\plugins\DbLog', 'beforePerform']],
        'afterPerform' => [['\plugins\DbLog', 'afterPerform']],
        'onFailure' => [['\plugins\DbLog', 'onFailure']],
    ],
    'DB' => [//数据库链接用来记录jon运行
        'dsn' => 'mysql:host=127.0.0.1;dbname=resque;charset=utf8',
        'usr' => 'root',
        'pwd' => '11111111',
    ],
];
