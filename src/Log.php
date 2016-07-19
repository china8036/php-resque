<?php

/**
 * @copyright (c) 2016, Ryan 简单的日志记录函数
 */
namespace core;

class Log
{

    /**
     * 日志记录
     * @param string $file
     * @param mixed $msg
     */
    public static function record($file, $msg)
    {
        $dir = BASE_ROOT . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR . date('d');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (is_array($msg)) {
            $msg = var_export($msg, true);
        }
        $msg = "<?php exit;?> \r\n" . date('Y-m-d H:i:s') . ':' . $msg . "\r\n";
        $log_file_size = 2097152;
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        $filename = $dir . DIRECTORY_SEPARATOR . $file . '.php';
        if (is_file($filename) && floor($log_file_size) <= filesize($filename)) {
            rename($filename, $dir . DIRECTORY_SEPARATOR . $file . '_' . time() . '.php');
        }
        file_put_contents($filename, $msg, FILE_APPEND);
    }

}
