<?php

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 */
return [
    'redis_backend' => '127.0.0.1:6379',
    'redis_backend_db' => null,//redis 数据库名称
    'queue' => [//监控的任务队列， 和监控的进程数 多个用','分开 '*'为监控所有队列
        ['crontab', 1],//定时任务占用
        ['high',1],
        ['high,normal',2],
        ['high,normal,low', 3],
    ]
];
