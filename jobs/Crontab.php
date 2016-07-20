<?php

/**
 * @copyright (c) 2916, Ryan [CHAOMA.ME]
 */
use Resque;
use core\Log;

class Crontab implements core\intface\Job
{

    public function perform()
    {
        sleep(1);
        $jobId = Resque::enqueue('crontab', 'Crontab', ['time' => microtime(true)], true); //循环投递用来模拟定时器 测一下延时
        Log::record('crontab', [$jobId, $this->args]);
    }

}
