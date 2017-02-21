<?php
/**
 * 动态对象管理器
 * Author: Marico
 * Date: 2016/12/23
 * Time: 11:49
 */
class Object_Manager
{
    // 存储Object对象
    private static $obj = [];

    /**
     * 获取对象
     * @param string $className
     * @param string $module
     * @param string $addons
     * @return Object
     */
    public static function Get($className='', $module='Controller', $addons='Index')
    {
        $key = self::makeKey($className, $module, $addons);
        // 判断对象是否存在，不存在则实例化对象
        if (isset(self::$obj[$key])) //&& self::Check($key))
        {
            return self::$obj[$key];
        }
        return self::Make($key, $className, $module, $addons);
    }

    /**
     * 销毁一个对象
     * @param string $className
     * @param string $module
     * @return null
     */
    public static function Destroy($className='', $module='Controller', $addons='Index')
    {
        $key = self::makeKey($className, $module, $addons);
        unset(self::$obj[$key]);
    }

    /**
     * 观察模式，批量通知
     * @param string $method
     * @param array $data 传入数据
     * @return null
     */
    public static function Observer($method, $data=[])
    {
        foreach (self::$obj as $key => &$v)
        {
            if (method_exists($v, $method))
            {
                call_user_func_array([$v, $method], $data);
            }
            unset(self::$obj[$key]);
        }
    }

    /**
     * 制作一个对象
     * @param string $key
     * @param string $className
     * @param string $module
     * @param string $addons 插件模块
     * @return Object
     */
    private static function Make($key='', $className='', $module='', $addons='')
    {
        // 获取对象所在路径
        $path = self::Path($className, $module, $addons);
        // 导入文件
        Yaf_Loader::import($path);
        // 实例化类
        self::$obj[$key] = new $className;
        return self::$obj[$key];
    }

    /**
     * 获取一个对象的路径
     * @param string $className
     * @param string $module
     * @return string
     * @throws Exception
     */
    private static function Path($className='', $module='', $addons='')
    {
        $path = '';
        // PATH路径分析
        switch ($module)
        {
            case 'controller':;
                $path = APP_PATH.'/addons/'.$addons.'/'.$className.'.php';
                break;
            default :
                throw new Exception('不存在此'.$module.'类型');
        }
        return $path;
    }

    /**
     * 规范名称
     * @param string $className
     * @param string $module
     * @param string $addons 插件
     * @return string
     */
    private static function makeKey(&$className='', &$module='Controller', &$addons='index')
    {
        $addons = ucfirst(strtolower($addons));
        $className = ucfirst(strtolower($className));
        $module = strtolower($module);
        return $addons .'.'. $className .'.'. $module;
    }

}