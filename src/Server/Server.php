<?php
/**
 * Created by PhpStorm.
 * User: daryl
 * Date: 2017/7/20
 * Time: 下午11:29
 */

namespace AtomSwoole\Server;

use AtomSwoole\Contracts\ServerInterface;
use swoole_server as SwooleServer;

class Server implements ServerInterface
{
    protected $config = [
        'host' => '0.0.0.0',
        'port' => '9501',
        'mode' => SWOOLE_PROCESS,
        'sockType' => SWOOLE_SOCK_TCP,
    ];

    protected $setting = [
        'worker_num' => 4,
        'daemonize' => true,
        'backlog' => 128,
    ];

    protected $server;

    public function __construct()
    {
        $class = new \ReflectionClass(SwooleServer::class);
        $this->server = $class->newInstanceArgs($this->config);
        $this->server->set($this->setting);

        $this->server->on('connect', [$this, 'onConnect']);
        $this->server->on('receive', [$this, 'onReceive']);
    }

    protected function onConnect($server, $fd)
    {
        var_dump($server, $fd);
    }

    protected function onReceived($server, $fd, $fromId, $data)
    {

    }

    public function __call($methodName, $args)
    {
        if (method_exists($this->server, $methodName)) {
            call_user_func([$this->server, $methodName], $args);
        }
    }
}