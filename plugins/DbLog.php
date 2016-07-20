<?php

/**
 * 尝试记录每次任务执行
 * 高并发情况下是否能正常记录？
 * 为了最大并发 不对表进行更新
 * 不对job_id做索引 以便更快的插入
 *  
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 */

namespace plugins;

use core\DB;

class Dblog
{

    private static $db;

    /**
     * 任务运行结果记录
     */
    const RESULT_LOG_TABLE = 'job_result_log';

    /**
     * 任务运行记录
     */
    const RUN_LOG_TABLE = 'job_run_log';

    
    /**
     * 监控任务运行
     * @param Resque $job
     */
    public static function beforePerform($job)
    {
        $log = [
            'job_id' => $job->payload['id'],
            'queue' => $job->queue,
            'job_name' => $job->payload['class'],
            'params' => json_encode($job->payload['args']),
        ];
        self::dbLog($log, self::RUN_LOG_TABLE);
    }

    /**
     * 监控任务运行结束
     * @param Resque $job
     */
    public static function afterPerform($job)
    {
        $log = [
            'job_id' => $job->payload['id'],
            'queue' => $job->queue,
            'job_name' => $job->payload['class'],
            'params' => var_export($job->payload['args'], true),
            'flag' => 1,
        ];
        self::dbLog($log);
    }

    
    /**
     * 任务运行失败 多在与抛出异常
     * @param \Exception $exception 抛出的异常
     * @param Resque $job
     */
    public static function onFailure(\Exception $exception, $job)
    {
        $log = [
            'job_id' => $job->payload['id'],
            'queue' => $job->queue,
            'job_name' => $job->payload['class'],
            'params' => var_export($job->payload['args'], true),
            'flag' => -1,
            'result' => $exception->getMessage() . $exception->getTraceAsString(),
        ];
        self::dbLog($log);
    }

    
    /**
     * 记录数据
     * @param array $data 记录的数据
     * @param string $table 记录到的表
     */
    private static function dbLog(array $data, $table = self::RESULT_LOG_TABLE)
    {
        if (!isset(self::$db[$table])) {
            self::$db[$table] = new DB($table);
        }
        self::$db[$table]->insert($data);
    }

}
