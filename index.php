<?php

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 */
register_shutdown_function(function(){
    var_dump(error_get_last());
});
define('BASE_ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
date_default_timezone_set('GMT');
include_once BASE_ROOT . DS . 'src' . DS . 'Initer.php';
\core\Initer::load();
$worker = new \core\Worker();
$worker->run('*', 1, 3);
