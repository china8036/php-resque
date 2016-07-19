<?php

if (empty($argv[1])) {
    die('Specify the name of a job to add. e.g, php queue.php PHP_Job');
}
define('BASE_ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);
include_once BASE_ROOT . DS . 'src' . DS . 'Core.php';
\core\Core::load();
Resque::setBackend('127.0.0.1:6379');

$args = array(
    'time' => time(),
    'array' => array(
        'test' => 'test',
    ),
);

$jobId = Resque::enqueue($argv[1], $argv[2], $args, true);
echo "Queued job " . $jobId . "\n\n";
