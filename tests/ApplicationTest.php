<?php

/**
 * Created by PhpStorm.
 * User: daryl
 * Date: 2017/7/13
 * Time: 上午12:01
 */
class ApplicationTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function a_application_can_make_object()
    {
        $app = new \AtomSwoole\Foundation\Application();
        $this->assertEquals(new Fuck(2333), $app->make(Fuck::class, [2333]));
    }

    /** @test */
    public function make_method_not_singleton_without_third_parameter_true()
    {
        $app = new \AtomSwoole\Foundation\Application();
        $fuck = $app->make(Fuck::class, [2333]);
        $fuck->setTest(3444);
        $this->assertNotEquals($fuck, $app->singleton(Fuck::class, [2333]));
    }

    /** @test */
    public function a_application_can_make_singleton_object()
    {
        $app = new \AtomSwoole\Foundation\Application();
        $fuck = $app->singleton(Fuck::class, [2333]);
        $fuck->setTest(3444);
        $this->assertEquals($fuck, $app->singleton(Fuck::class, [2333]));
    }
}

class Fuck
{
    protected $test;

    public function __construct($test)
    {
        $this->test = $test;
    }

    /**
     * @param mixed $test
     */
    public function setTest($test)
    {
        $this->test = $test;
    }
}
