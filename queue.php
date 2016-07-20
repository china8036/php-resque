<?php

define('BASE_ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
include_once BASE_ROOT . DS . 'src' . DS . 'Core.php';
\core\Core::load();
Resque::setBackend('127.0.0.1:6379');



for ($i = 0; $i < 20; $i++) {
    $jobId = Resque::enqueue('low', 'Crontab', [$i], true);
}
echo "Done";
