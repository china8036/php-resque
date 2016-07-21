<?php

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 * 简易的应用框架
 * 主要实现自动加载\错误处理\配置读取
 */

namespace core;

class Core
{

    /**
     * 配置信息
     * @var array 
     */
    private static $config = [];

    /**
     * 加载默认配置
     */
    public static function load()
    {
        self::initEnv();
        self::loadConfig();
        self::initAutoload();
        self::loadPlugins();
    }

    /**
     * 设置环境
     */
    public static function initEnv()
    {
        error_reporting(E_ERROR);
        date_default_timezone_set('PRC');
        register_shutdown_function(['core\Core', 'handleShutdown']);
        set_exception_handler(['core\Core', 'handleException']);
    }

    /**
     * 加载配置
     */
    public static function loadConfig()
    {
        self::$config = include BASE_ROOT . DS . 'config' . DS . 'config.php';
    }

    /**
     * 注册自动加载类
     */
    public static function initAutoload()
    {
        include_once BASE_ROOT . DS . 'vendor' . DS . 'Psr' . DS . 'ClassLoader.php';
        \Psr\ClassLoader::init();
        \Psr\ClassLoader::register(BASE_ROOT . DS . 'vendor');
        \Psr\ClassLoader::register(BASE_ROOT);
        \Psr\ClassLoader::map('core', __DIR__);
    }

    /**
     * 
     *  afterEnqueue  
     *  beforeFirstFork beforeFork 
     *  afterFork beforePerform afterPerform onFailure
     *  注册插件
     * @return type
     */
    public static function loadPlugins()
    {
        $plugin_setting = self::c('plugins');
        if (empty($plugin_setting)) {
            return;
        }
        foreach ($plugin_setting as $hook => $plugins) {
            if (empty($plugins)) {
                continue;
            }
            foreach ($plugins as $plugin) {
                \Resque_Event::listen($hook, $plugin);
            }
        }
    }

    /**
     * 捕获异常
     * @param type $exception
     */
    public static function handleException(\Exception $exception)
    {
        Log::record('exception', 'have exception: ' .$exception->getMessage() . "\r\n". $exception->getTraceAsString());
        //exit;
    }

    /**
     * 错误捕捉
     */
    public static function handleShutdown()
    {
        $err = error_get_last();
        if (!empty($err)) {
            Log::record('shutdown', var_export($err, true));
        }
    }

    /**
     * 读取指定名称的配置项
     * @param string $name
     * @param bool $force
     * @return mixed
     */
    public static function c($name, $force = false)
    {
        if (empty(self::$config) || $force) {
            self::loadConfig();
        }
        if (!isset(self::$config[$name])) {
            return NULL;
        }
        return self::$config[$name];
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
