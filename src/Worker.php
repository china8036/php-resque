<?php

/**
 * php resque 的机制是 投递job到多个队列中的一个队列，
 * worker可以注册监控那个队列 并会按设定的时间（完成一个任务后等待时间）去设置的队列中获取任务 
 * 然后执行任务投递的 任务类的perform
 * 任务worker类的注册就是自动加载 或者显示的include/require
 * 
 */

namespace core;

use Resque;
use Resque_Log;
use Resque_Worker;
use Psr\Log\LogLevel;

class Worker
{

    public static function run($queue)
    {
        $QUEUE = $queue;

        /**
         * REDIS_BACKEND can have simple 'host:port' format or use a DSN-style format like this:
         * - redis://user:pass@host:port
         *
         * Note: the 'user' part of the DSN URI is required but is not used.
         */
        $REDIS_BACKEND = getenv('REDIS_BACKEND'); //redis的链接信息
// A redis database number
        $REDIS_BACKEND_DB = getenv('REDIS_BACKEND_DB');
        if (!empty($REDIS_BACKEND)) {
            if (empty($REDIS_BACKEND_DB))
                Resque::setBackend($REDIS_BACKEND);
            else
                Resque::setBackend($REDIS_BACKEND, $REDIS_BACKEND_DB);
        }

        $logLevel = false;
        $LOGGING = getenv('LOGGING'); //基本日志记录
        $VERBOSE = getenv('VERBOSE'); //啰嗦日志记录
        $VVERBOSE = getenv('VVERBOSE'); //更啰嗦日志记录
        if (!empty($LOGGING) || !empty($VERBOSE)) {
            $logLevel = true;
        } else if (!empty($VVERBOSE)) {
            $logLevel = true;
        }

        $APP_INCLUDE = getenv('APP_INCLUDE');
        if ($APP_INCLUDE) {
            if (!file_exists($APP_INCLUDE)) {
                die('APP_INCLUDE (' . $APP_INCLUDE . ") does not exist.\n");
            }

            require_once $APP_INCLUDE;
        }

// See if the APP_INCLUDE containes a logger object,
// If none exists, fallback to internal logger
        if (!isset($logger) || !is_object($logger)) {//注册日志记录
            $logger = new Resque_Log($logLevel);
        }

        $BLOCKING = getenv('BLOCKING') !== FALSE;

        $interval = 5; //完成任务后默认休眠时间
        $INTERVAL = getenv('INTERVAL');
        if (!empty($INTERVAL)) {
            $interval = $INTERVAL;
        }

        $count = 1;
        $COUNT = getenv('COUNT'); //启动的worker数目 可以同时启动多个worker进程
        if (!empty($COUNT) && $COUNT > 1) {
            $count = $COUNT;
        }

        $PREFIX = getenv('PREFIX');
        if (!empty($PREFIX)) {
            $logger->log(LogLevel::INFO, 'Prefix set to {prefix}', array('prefix' => $PREFIX));
            Resque_Redis::prefix($PREFIX);
        }

        if ($count > 1) {
            for ($i = 0; $i < $count; ++$i) {
                $pid = Resque::fork();
                if ($pid == -1) {
                    $logger->log(LogLevel::EMERGENCY, 'Could not fork worker {count}', array('count' => $i));
                    die();
                }
                // Child, start the worker
                else if (!$pid) {
                    $queues = explode(',', $QUEUE); //监控的队列
                    $worker = new Resque_Worker($queues);
                    $worker->setLogger($logger);
                    $logger->log(LogLevel::NOTICE, 'Starting worker {worker}', array('worker' => $worker));
                    $worker->work($interval, $BLOCKING);
                    break;
                }
            }
        }
// Start a single worker
        else {
            $queues = explode(',', $QUEUE);
            $worker = new Resque_Worker($queues);
            $worker->setLogger($logger);

            $PIDFILE = getenv('PIDFILE');
            if ($PIDFILE) {
                file_put_contents($PIDFILE, getmypid()) or
                        die('Could not write PID information to ' . $PIDFILE);
            }

            $logger->log(LogLevel::NOTICE, 'Starting worker {worker}', array('worker' => $worker));
            $worker->work($interval, $BLOCKING);
        }
    }

}
