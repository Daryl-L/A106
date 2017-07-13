<?php
/**
 * Created by PhpStorm.
 * User: daryl
 * Date: 2017/7/6
 * Time: 下午5:04
 */

namespace AtomSwoole\Foundation;

class Application extends Container
{
    public function __construct()
    {
        self::$instance = $this;
    }
}