<?php

/**
 * php resque 的机制是 投递job到多个队列中的一个队列，
 * worker可以注册监控那个队列 并会按设定的时间（完成一个任务后等待时间）去设置的队列中获取任务 
 * 然后执行任务投递的 任务类的perform
 * 任务worker类的注册就是自动加载 或者显示的include/require
 * 任务执行时进程会fork一个子进程 这样就可以避免业务代码异常退出引起job退出
 * 
 */

namespace core;

use Resque;

class Crontab
{

    /**
     * 定时任务队列queue名
     */
    const CRONTAB_QUEUE = 'crontab';

    /**
     * 定时的单位
     */
    const TIMING_SECOND = 1;

    /**
     * 一分钟的秒数
     */
    const PER_MIN_SECONDS = 60;

    /**
     * 经过的秒数
     * @var static 
     */
    static $pass_seconds = 0;

    /**
     * 经过的分钟数
     * @var int 
     */
    static $pass_minunts = 0;

    /**
     * 定时任务
     * @var array
     */
    static $tasks = [];

    /**
     * 开启服务
     * @param array $tasks 任务描述
     */
    public static function run(array $tasks)
    {
        $pid = pcntl_fork();
        if (!$pid) {//由于要死循环阻塞 所以让子进程做这件事情
            self::updateProcLine();//更新进程Title
            self::$tasks = $tasks;
            self::installHandler();
            pcntl_alarm(self::TIMING_SECOND);
            while (true) {
                sleep(self::TIMING_SECOND);
                pcntl_signal_dispatch();
            }
        }
    }

    /**
     * 注册信号处理函数
     */
    public static function installHandler()
    {
        pcntl_signal(SIGALRM, array('core\Crontab', 'signalHandler'));
    }

    /**
     * 信号处理函数
     */
    public static function signalHandler()
    {
        pcntl_alarm(self::TIMING_SECOND);
        self::$pass_seconds ++;
        if (!(self::$pass_seconds % self::PER_MIN_SECONDS)) {
            self::$pass_minunts++;
        }
        self::task();
    }

    /**
     * 扫描定时任务
     * 符合条件投递到crontabl列队
     */
    public static function task()
    {
        foreach (self::$tasks as $mode => $jobs) {
            if (!self::isMatchCrontabTime($mode)) {
                continue;
            }
            foreach ($jobs as $job) {
                $jobid[] = Resque::enqueue(self::CRONTAB_QUEUE, $job, [self::$pass_seconds], true);
            }
            Log::record(self::CRONTAB_QUEUE, [$jobid, $mode, $jobs]);
        }
    }

    /**
     * 是否匹配时间要求
     * @param string $mode 匹配字符串
     * @return boolean
     */
    public static function isMatchCrontabTime($mode)
    {
        $tmode = trim($mode);
        if (strpos($tmode, '-')) {//月日
            return self::isM1Time($tmode);
        } elseif (strpos($tmode, ' ')) {//周
            return self::isM2Time($tmode);
        }
        return self::isM3Time($tmode);
    }

    /**
     * '*-09 16:00' 月 日 小时 分钟
     * @param string $mode 匹配字符串
     * @return boolean
     */
    public static function isM1Time($mode)
    {
        list($md, $hm) = explode(' ', $mode);
        list($mouth, $day) = explode('-', $md);
        list($hour, $min, $sec) = explode(':', $hm);
        if ($mouth != '*' && $mouth != date('m')) {//不符合
            return false;
        }
        if ($day != '*' && $day != date('d')) {
            return false;
        }
        if ($hour != '*' && $hour != date('H')) {
            return false;
        }
        if ($min != '*' && $min != date('i')) {
            return false;
        }
        if ($sec != date('s')) {
            return false;
        }
        return true;
    }

    /**
     * 1 16:01 每周几 小时 分钟
     * @param string $mode 匹配字符串
     * @return boolean
     */
    public static function isM2Time($mode)
    {
        list($week, $hm) = explode(' ', $mode);
        list($hour, $min, $sec) = explode(':', $hm);
        if ($week != date('N')) {
            return false;
        }
        if ($hour != date('H')) {
            return false;
        }
        if ($min != date('i')) {
            return false;
        }
        if ($sec != date('s')) {
            return false;
        }
        return true;
    }

    /**
     * 每隔多少秒
     * @param string $mode 匹配字符串
     * @return boolean
     */
    public function isM3Time($mode)
    {
        $seconds = intval($mode);
        if (self::$pass_seconds % $seconds) {
            return false;
        }
        return true;
    }

    /**
     * 设置进程标题 为了和 resque的worker名称统一 引入
     * @param string $status The updated process title.
     */
    private static function updateProcLine()
    {
        $processTitle = 'resque-' . Resque::VERSION . ': Crontab Alarm proc';
        if (function_exists('cli_set_process_title') && PHP_OS !== 'Darwin') {
            cli_set_process_title($processTitle);
        } else if (function_exists('setproctitle')) {
            setproctitle($processTitle);
        }
    }

}
