<?php

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 */
register_shutdown_function(function(){
    var_dump(error_get_last());
});
define('BASE_ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
include_once BASE_ROOT . DS . 'src' . DS . 'Initer.php';
\core\Initer::load();
$worke = new \core\Work();
$worke->run('*', 1, 3);
