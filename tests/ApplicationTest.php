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
    public function a_application_can_make_singleton_object()
    {
        $app = new \AtomSwoole\Foundation\Application();
        $app->singleton(PoiInterface::class, Poi::class);
        $app->singleton(MoeInterface::class, Moe::class);
        $moe = $app->make(MoeInterface::class);
        $poi = $app->make(PoiInterface::class);
        $this->assertEquals($moe, $poi->getMoe());
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

    public function getMoe()
    {
        return $this->moe;
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
