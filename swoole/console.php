<?php
/**
 * 服务控制台
 * @author: Marico
 * @date: 2016/12/13
 * @time: 10:36
 */
use Swoole\Client;


// 获取设置参数
$param = getopt('s:h:p:');
isset($param['s']) || die("缺少s指令，s指令用于指明具体操作\n");
in_array($param['s'], ['start', 'stop', 'reload']) || die("s指令仅可用start|stop|reload\n");
// 判断是否为开启服务
if ($param['s'] == 'start')
{
    \Yaf_Loader::import(__DIR__.'/init.php');
    return false;
}
// 否则发送通知
isset($param['h']) || $param['h'] = '127.0.0.1';
isset($param['p']) || $param['p'] = 10001;
$client = new Client(SWOOLE_SOCK_TCP);
if (!$client->connect($param['h'], $param['p'], 0.5))
{
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send(json_encode([
    'method' => $param['s'],
]));
$result = $client->recv();
var_dump(json_decode($result, true));
$client->close();
