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
        $app->bind(PoiInterface::class, Poi::class);
        $app->bind(MoeInterface::class, Moe::class);
        $moe = new Moe();
        $this->assertEquals($moe, $app->make(PoiInterface::class));
    }

    /** @test */
    public function make_method_not_singleton_without_third_parameter_true()
    {
        $app = new \AtomSwoole\Foundation\Application();
        $poi = $app->resolve(Poi::class, [2333]);
        $poi->setTest(3444);
        $this->assertNotEquals($poi, $app->singleton(Poi::class, [2333]));
    }

    /** @test */
    public function a_application_can_make_singleton_object()
    {
        $app = new \AtomSwoole\Foundation\Application();
        $poi = $app->singleton(Poi::class, [2333]);
        $poi->setTest(3444);
        $this->assertEquals($poi, $app->singleton(Poi::class, [2333]));
    }
}

interface PoiInterface
{

}

class Poi implements PoiInterface
{
    protected $moe;

    protected $test;

    public function __construct(MoeInterface $moe)
    {
        $this->moe = $moe;
    }

    /**
     * @param mixed $test
     */
    public function setTest($test)
    {
        $this->test = $test;
    }
}

interface MoeInterface
{

}

class Moe implements MoeInterface
{
    public function __construct()
    {
        
    }
}
