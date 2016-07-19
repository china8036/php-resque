<?php

/**
 * @copyright (c) 2016, Ryan 日志记录
 */

namespace core;

use Resque_Log;
use Psr\Log\LogLevel;

class Log extends Resque_Log
{

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed   $level    PSR-3 log level constant, or equivalent string
     * @param string  $message  Message to log, may contain a { placeholder }
     * @param array   $context  Variables to replace { placeholder }
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->verbose) {
            self::record($level, $this->interpolate($message, $context));
            return;
        }

        if (!($level === LogLevel::INFO || $level === LogLevel::DEBUG)) {
            self::record($level, $this->interpolate($message, $context));
        }
    }

    /**
     * 日志记录
     * @param string $file
     * @param mixed $msg
     */
    public static function record($file, $msg)
    {
        $dir = BASE_ROOT . DS . 'log' . DS . date('Y') . DS . date('m') . DS . date('d');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (is_array($msg)) {
            $msg = var_export($msg, true);
        }
        $msg = "<?php exit;?> \r\n" . date('Y-m-d H:i:s') . ':' . $msg . "\r\n";
        $log_file_size = 2097152;
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        $filename = $dir . DS . $file . '.php';
        if (is_file($filename) && floor($log_file_size) <= filesize($filename)) {
            rename($filename, $dir . DS . $file . '_' . time() . '.php');
        }
        file_put_contents($filename, $msg, FILE_APPEND);
    }

}
