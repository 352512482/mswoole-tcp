<?php
/**
 * Created by PhpStorm.
 * User: Marico
 * Date: 2017/1/19
 * Time: 16:58
 */

use Swoole\Server;

class App
{
    // server对象
    public $server;

    /**
     * App constructor.
     * @param Array $config
     * @param none
     */
    public function __construct(Array $config=[])
    {
        // 注册公共加载
        $this->autoLoad();
        // 初始化server
        $common = $config['common'];
        unset($config['common']);

        // 初始化服务,并监听内部管理,启动task进程
        $server = new Server($common['host'], $common['port']);
        $server->set($common['set']);
        $server->on('Connect', [$this, 'onConnect']);
        $server->on('Receive', [$this, 'onManager']);
        $server->on('Close', [$this, 'onClose']);
        // $server->on('task', [$this, 'onTask']); // 任务监听
        // $server->on('finish', [$this, 'onFinish']); // 任务结束监听

        // 外部端口监听
        $outside = $config['outside'];
        $other = $server->listen($outside['host'], $outside['port'], SWOOLE_SOCK_TCP);
        $other->on('Receive', [$this, 'onReceive']);

        // 全局化,并启动
        $this->server = $server;
        $this->server->start();
    }

    /**
     * 注册自动加载机制
     * @param none
     * @return none;
     */
    private function autoLoad()
    {
        // 注册自动加载机制
        spl_autoload_register(function($class){
            // 下划线视为一层目录
            $class = str_replace('_', '/', $class);
            // 判断是否为Model加载
            if (strpos($class, 'Model')>0)
            {
                $class = str_replace('Model', '', $class);
                \Yaf_Loader::import(APP_PATH.'/model/'.$class.'.php');
            }
            else
            {
                \Yaf_Loader::import(APP_PATH.'/library/'.$class.'.php');
            }
        });
    }

    /**
     * 当链接打开时
     * @param $server
     * @param $request
     */
    public function onConnect(Server $server,  $fd, $from_id)
    {
    }

    /**
     * 关闭链接处理
     * @param Server $server
     * @param int $fd
     * @param int $from_id
     * @param string $object
     */
    public function onClose(Server $server, $fd=0, $from_id=0, $object='')
    {
    }

    /**
     * 请求处理
     * @param Server $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     * @return mixed
     */
    public function onReceive(Server $server, $fd=0, $from_id=0, $data='')
    {
        try
        {
            // 定义默认回复
            $result = '数据格式有误';
            // 数据进行json解码
            $data = json_decode($data, true);
            // 判断是否为数组
            if (is_array($data))
            {
                // 路由处理，根据method（method格式，用.表示层级）
                $result = $this->router($data);
            }
            // 返回数据
            $this->sendMessage($fd, 200, '处理完成', $result);
        }
        catch (Exception $e)
        {
            $this->sendMessage($fd, 500, '服务端数据错误', $e->getMessage());
        }
    }

    /**
     * 路由处理,将请求投递给相应的控制器
     * @param Request $request
     * @return mixed
     */
    private function router($data)
    {
        // 设置默认返回值
        $result = '服务端无返回值';
        // 进行数据过滤
        $this->data_filter($data);
        // 统一身份处理（暂不使用）

        // 准备空对象,默认数据
        $object = [];
        $addons = 'Index';
        $controller = 'Home';
        $method = 'index';
        // 进行数据解析
        isset($data['method']) || $data['method'] = 'index';
        $data['method'] = trim($data['method'], '.');
        $url = explode('.', $data['method']);
        unset($data['method']);
        // 判断URL请求层次
        switch (count($url))
        {
            case 1:
                $method = array_shift($url);
                break;
            case 2:
                $controller = array_shift($url);
                $method = array_shift($url);
                break;
            case 3:
                $addons = array_shift($url);
                $controller = array_shift($url);
                $method = array_shift($url);
                break;
            default:;
        }
        // 规整数据
        $addons = ucfirst(strtolower($addons));
        $controller = ucfirst(strtolower($controller));
        $method = strtolower($method);
        // /wheel/prize
        // 获取对应的内容
        $object = \Object_Manager::Get($controller, 'Controller', $addons);
        // 检查对象情况
        if (!is_object($object))
        {
            return '您请求的接口不存在';
        }
        // 检查对象情况，处理method，自动调用
        if (method_exists($object, $method))
        {
            $result = call_user_func_array([$object, $method], [$data]);
        }
        return $result;
    }

    /**
     * task任务，向所有连接投递消息
     * @param $server
     * @param $worker_id
     * @param $task_id
     * @param $data
     * @return string
     */
    public function onTask(Server $server, $worker_id=0, $task_id=0, $data)
    {
        return $task_id;
    }

    /**
     * task任务结束
     * @param $server
     * @param $task_id
     * @param $result
     * @return bool
     */
    public function onFinish(Server $server, $task_id, $result)
    {
        return $task_id;
    }

    /**
     * 内部消息处理,热重启,热更新
     * @param Server $server
     * @param int $fd
     * @param int $from_id
     * @param string $data
     * @return bool
     */
    public function onManager(Server $server, $fd=0, $from_id=0, $data='')
    {
        // json格式解码
        $data = json_decode($data, true);
        if (empty($data) || !isset($data['method']))
        {
            return $this->sendMessage($fd, 404, '格式不符合规范');
        }
        // 判断操作类型
        switch ($data['method'])
        {
            case 'reload': // 服务重启
                \Object_Manager::Observer('onReload'); // 通知所有管理对象，进行整体重启
                $this->sendMessage($fd, 200, '服务即将重启');
                $server->reload();
                break;
            case 'stop' : // 停止服务
                \Object_Manager::Observer('onShutdown'); // 通知所有管理对象，进行整体关闭
                $this->sendMessage($fd, 200, '服务即将关闭');
                $server->shutdown();
                break;
            default :
                $this->sendMessage($fd, 404, '无效操作');
        }
    }

    /**
     * 发送信息
     * @param int $fd
     * @param int $status
     * @param string $info
     * @param array $param
     * @return bool
     */
    public function sendMessage($fd=0, $status=0, $info='', $param=[])
    {
        // 若为空，则不处理
        if (empty($fd)){return false;}
        // 准备json数据
        $data = [
            'status' => $status,
            'info' => $info,
        ];
        // 若参数不为空，则检查处理
        if (!empty($param))
        {
            if (is_string($param))
            {
                $data['param'] = $param;
            }
            else if (is_array($param))
            {
                $data['param'] = $param;
                isset($param['status']) && $data['status'] = $param['status'];
                isset($param['info']) && $data['info'] = $param['info'];
                isset($param['param']) && $data['param'] = $param['param'];
            }
        }
        // 判断是否为纯数字，纯数字则视为向一个用户发送消息
        if (is_numeric($fd))
        {
            // 判断用户是否还在线
            $this->server->exist($fd) && $this->server->send($fd, json_encode($data));
        }
    }

    /**
     * 请求参数安全过滤
     * @param $data
     */
    private function data_filter(&$data)
    {
        // 判断是否为字符串
        if (is_string($data))
        {
            $data = htmlspecialchars($data);
        }
        // 判断是否为数组
        if (is_array($data))
        {
            foreach ($data as $key => &$value)
            {
                $this->data_filter($value);
            }
        }
    }
}