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
        $app->bind(YupInterface::class, Yup::class);
        $this->assertEquals(true, $app->make(PoiInterface::class) instanceof Poi);
    }

    /** @test */
    public function a_application_can_make_singleton_object()
    {
        $app = new \AtomSwoole\Foundation\Application();
        $app->singleton(PoiInterface::class, Poi::class);
        $app->singleton(MoeInterface::class, Moe::class);
        $app->singleton(YupInterface::class, Yup::class);
        $moe = $app->make(MoeInterface::class);
        $poi = $app->make(PoiInterface::class);
        $this->assertEquals($moe, $poi->getMoe());
    }

    /** @test */
    public function a_application_can_throw_exception_while_dependency_not_found()
    {
        $this->expectException(\AtomSwoole\Exceptions\ContainerException::class);
        $app = new \AtomSwoole\Foundation\Application();
        $app->singleton(PoiInterface::class, Poi::class);
        $app->make(PoiInterface::class);
    }
}

interface PoiInterface
{

}

class Poi implements PoiInterface
{
    protected $moe;

    protected $test;

    protected $yup;

    public function __construct(MoeInterface $moe, YupInterface $yup)
    {
        $this->moe = $moe;
        $this->yup = $yup;
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

interface YupInterface
{

}

class Yup implements YupInterface
{

}