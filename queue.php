<?php

define('BASE_ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
include_once BASE_ROOT . DS . 'src' . DS . 'Core.php';
\core\Core::load();
Resque::setBackend('127.0.0.1:6379');



for ($i = 0; $i < 200; $i++) {
    $jobId = Resque::enqueue('high', 'Crontab', [$i], true);
}
echo "Done";
