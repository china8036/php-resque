<?php

/**
 * 兼容psr0 和psr4 的加载机制 
 * 并且支持路径挂载 和 路径映射
 * @copyright (c) 2016, Ryan [CHAOMA.me]
 */

namespace Psr;

class ClassLoader
{

    /**
     * 映射的类
     * @var array
     */
    private static $map = [];

    /**
     * 加载路径 类似于环境变量
     * @var type 
     */
    private static $path = [];

    /**
     * 初始化自动加载
     */
    public static function init()
    {
        spl_autoload_register([self, 'autoload']);
    }

    /**
     * 加载 
     * 先对映射进行扫描 加载不成功进行下一步操作
     * 对注册的路径扫描加载 一旦加载成功即退出
     * @param string $class 类名
     */
    public static function autoload($class)
    {
        foreach (self::$map as $pre => $path) {
            if (strpos($class, $pre) === 0) {
                return self::psr(str_replace('xx' . $pre, '', 'xx' . $class), $path);
            }
            
        }
        foreach (self::$path as $dir) {
            if (self::psr($class, $dir)) {
                return true;
            }
        }
    }

    /**
     *  psr4 psr0标准加载
     * @param string $class 类名
     * @param string $dir 基础路径
     * @return boolean
     */
    public static function psr($class, $dir)
    {
        if (self::psr4($class, $dir)) {
            return true;
        }
        if (self::psr0($class, $dir)) {
            return true;
        }
        return false;
    }

    /**
     *  psr0标准加载
     * @param string $class 类名
     * @param string $dir 基础路径
     * @return boolean
     */
    public static function psr0($class, $dir)
    {
        $class_file = $dir . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
        if (file_exists($class_file)) {//psr-0标准
            require $class_file;
            return true;
        }
        return false;
    }

    /**
     *  psr4标准加载
     * @param string $class 类名
     * @param string $dir 基础路径
     * @return boolean
     */
    public static function psr4($class, $dir)
    {
        $class_namespace_file = $dir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        if (file_exists($class_namespace_file)) {//psr-4 标准
            require $class_namespace_file;
            return true;
        }
        return false;
    }

    /**
     * 注册加载的环境变量
     * @param string $path
     */
    public static function register($path)
    {
        if (in_array($path, self::$path)) {
            return true;
        }
        self::$path[] = $path;
    }

    /**
     * 注册地址映射
     * @param 前缀 $pre
     * @param 映射的路径 $path
     */
    public static function map($pre, $path)
    {
        self::$map[$pre] = $path;
    }

}
