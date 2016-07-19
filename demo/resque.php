<?php
date_default_timezone_set('GMT');
// Find and initialize Composer
include_once dirname(__DIR__) . '/lib/Psr/ClassLoader.php';
\Psr\ClassLoader::init();
\Psr\ClassLoader::register(dirname(__DIR__) . '/lib');

require 'bad_job.php';
require 'job.php';
require 'php_error_job.php';

require '../bin/resque';