<?php

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 */
define('BASE_ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
define('APP_DEBUG', false);//开启debug将会输出详细的日志记录
define('CRONTAB_CONFIG_FILE', BASE_ROOT . DS . 'config' . DS . 'crontab.php');//定时任务
include_once BASE_ROOT . DS . 'src' . DS . 'Core.php';
use core\Core as Core;
use core\Work as Work;
Core::load();
$work = new Work(Core::c('redis_backend'), Core::c('redis_backend_db'), Core::c('prefix'));
$work->queue(Core::c('queue'));

