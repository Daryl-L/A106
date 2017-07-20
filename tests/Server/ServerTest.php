<?php

/**
 * Created by PhpStorm.
 * User: daryl
 * Date: 2017/7/20
 * Time: 下午11:53
 */

use AtomSwoole\Server\Server;

class ServerTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function server_can_listen_a_port()
    {
        $server = new Server();
        $server->start();
    }
}
