<?php

/*
 *
 *  简易的应用框架
 * 主要实现自动加载\错误处理\配置读取
 */

namespace core;

class Initer
{

    /**
     * 加载默认配置
     */
    public static function load()
    {
        self::initEnv();
        self::initAutoload();
    }

    /**
     * 
     */
    public static function initEnv()
    {
        error_reporting(E_ERROR);
        date_default_timezone_set('PRC');
        register_shutdown_function([self, 'handleShutdown']);
    }

    /**
     * 
     */
    public static function initAutoload()
    {
        include_once BASE_ROOT . DS . 'vendor' . DS . 'Psr' . DS . 'ClassLoader.php';
        \Psr\ClassLoader::init();
        \Psr\ClassLoader::register(dirname(__DIR__) . DS . 'vendor');
        \Psr\ClassLoader::map('core', __DIR__);
    }

    /**
     * 捕获异常
     * @param type $exception
     */
    public static function handleException(Exception $exception)
    {
        Log::record('worker_ex', 'have exception: ' . $exception->getTraceAsString());
        exit;
    }

    /**
     * 
     */
    public static function handleShutdown()
    {
        $err = error_get_last();
        var_dump($err);
        if (!empty($err)) {
            Log::record('shutdown', var_export($err, true));
            in_array($err['type'], self::getPHPErrorTypes()) && sleep(29);
            sleep(1);
        }
    }

    /**
     * 读取指定名称的配置项
     * @staticvar array $config
     * @param string $name
     * @param bool $force
     * @return mixed
     */
    public static function C($name, $force = false)
    {
        static $config;
        if (empty($config) || $force) {
            $config = self::loadConfig('common');
        }
        if (!isset($config[$name])) {
            return NULL;
        }
        return $config[$name];
    }

    /**
     * 
     * @return type
     */
    public static function getPHPErrorTypes()
    {
        return array(
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_USER_ERROR,
            E_RECOVERABLE_ERROR,
        );
    }

    /**
     * 断点调试上下文
     */
    public static function setBreakpoint()
    {
        echo '<pre>';
        print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
        echo '</pre>';
        exit;
    }

}
