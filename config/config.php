<?php

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 * 基本配置
 */
return [
    'redis_backend' => '127.0.0.1:6379',
    'redis_backend_db' => null, //redis 数据库名称
    'prefix' => null, //前缀 多个php-resque时候用于区分
    'queue' => [//监控的任务队列， 和监控的进程数 多个用','分开 '*'为监控所有队列
        ['high,normal,low', 3], //先后循序标示优先级
        ['normal,high,low', 2],
        ['low,high,normal', 1]
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
    'CMB' => [//用于oauth2接口调用
        'host' => 'http://api.test.chaoma.me',
        'access_token' => 'ba21a2368cdeea0292c8a2060143a6ffff8e55a8fd2',
    ],
    //用于切换到oao系统代码目录,执行其中的程序
    'OAOENV' => [
        'root_path' => '/var/www/wmd',
        'php_ini_path' => '/etc/php/php-cli.ini',
        'php_cli_path' => '/usr/local/bin/php'
    ],
];
