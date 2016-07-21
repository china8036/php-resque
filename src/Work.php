<?php

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 * 
 * php resque 的机制是 投递job到多个队列中的一个队列，
 * worker可以注册监控那个队列 并会按设定的时间（完成一个任务后等待时间）去设置的队列中获取任务 
 * 然后执行任务投递的 任务类的perform
 * 任务worker类的注册就是自动加载 或者显示的include/require
 * 任务执行时进程会fork一个子进程 这样就可以避免业务代码异常退出引起job退出
 * 
 */

namespace core;

use Resque;
use Resque_Log;
use Resque_Redis;
use Resque_Worker;
use Psr\Log\LogLevel;

class Work
{

    /**
     * 超时重新阻塞时间 只要不是0 就可以?
     */
    const BLOCK_TIMEOUT = 10;

    /**
     * 构造函数
     * @param string $redis_backend redis链接配置
     * @param string $redis_backend_db db
     * @param string $prefix 前缀
     */
    public function __construct($redis_backend, $redis_backend_db = null, $prefix = null)
    {
        if ($redis_backend_db) {
            Resque::setBackend($redis_backend, $redis_backend_db);
        } else {
            Resque::setBackend($redis_backend);
        }
        if ($prefix) {
            Resque_Redis::prefix($prefix);
        }
    }

    /**
     * 运行队列
     * @param array $queues
     */
    public function queue(array $queues)
    {
        foreach ($queues as $queue_setting) {//统一设置为阻塞索要任务 无超时时间
            $this->run($queue_setting[0], $queue_setting[1], self::BLOCK_TIMEOUT, true);
        }
        //运行定时任务队列 此队列专门存放定时任务投放的任务
        $this->run(Crontab::CRONTAB_QUEUE, 1, self::BLOCK_TIMEOUT, true); 
        //运行定时任务
        defined('CRONTAB_CONFIG_FILE') && Crontab::run(CRONTAB_CONFIG_FILE); 
    }

    /**
     * 根据设置运行worker
     * @param sting $queue 监控的队列
     * @param int $count 生成几个监控进程
     * @param int $interval 执行完任务的间隔时间 当阻塞时候为阻塞超时时间
     * @param  $block 是否阻塞索要任务
     * @return boolean
     */
    public function run($queue, $count, $interval, $block = false)
    {
        $logger = new Log(APP_DEBUG === true); //传true为啰嗦模式
        if ($count < 1) {
            $count = 1;
        }
        for ($i = 0; $i < $count; ++$i) {
            $pid = Resque::fork();
            if ($pid == -1) {//创建失败
                $logger->log(LogLevel::EMERGENCY, 'Could not fork worker {count}', array('count' => $i));
                exit;
            } elseif (!$pid) {//
                $this->work($queue, $logger, $interval, $block);
                exit; //子进程里面不需要再循环 return 的话如果此函数下面有逻辑很难控制
            }
            //父进程继续循环生成
        }
    }

    /**
     * 运行worker
     * @param string $queue 监控的队列
     * @param LogLevel $logger 日志记录
     * @param type $interval 间隔检查时间
     * @param type $block 是否阻塞
     */
    public function work($queue, Resque_Log $logger, $interval, $block)
    {
        $queues = explode(',', $queue);
        $worker = new Resque_Worker($queues);
        $worker->setLogger($logger);
        $logger->log(LogLevel::NOTICE, 'Starting worker {worker}', array('worker' => $worker));
        $worker->work($interval, $block);
    }

}
