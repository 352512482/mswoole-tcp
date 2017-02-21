test<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/16
 * Time: 16:28
 */
$data = [
    'method' => 'home.index',
    'say' => 'hello!'
];
// 创建客户端对象
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC); // 同步阻塞
// 发起连接
$client->connect('127.0.0.1', 10002, 0.5, 0);
// 提交数据(仅支持字符串提交)
$client->send(json_encode($data));
// 接收返回值
$data = $client->recv(1024);
var_dump(json_decode($data, true));
$client->close();
// 释放变量
unset($client);
unset($data);