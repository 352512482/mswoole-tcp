<?php
/**
 * 全局入口.
 * Author: Marico
 * Date: 2016/12/23
 * Time: 11:51
 */
// 定义全局常量
ini_set('date.timezone','Asia/Shanghai');
define("APP_PATH", realpath(dirname(__FILE__) . '/../'));
define("APP_DEBUG", false);

// 读取配置信息
$config = (new \Yaf_Config_Ini(APP_PATH.'/conf/server.ini'))->toArray();
empty($config) && die('配置server.ini为空');

// 读取App类所在的文件
\Yaf_Loader::import(__DIR__.'/App.php');
$app = new App($config);