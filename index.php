<?php

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 */
define('BASE_ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
include_once BASE_ROOT . DS . 'src' . DS . 'Core.php';
use core\Core as Core;
Core::load();
$work = new \core\Work(Core::c('redis_backend'), Core::c('redis_backend_db'));
$work->queue(Core::c('queue'));

