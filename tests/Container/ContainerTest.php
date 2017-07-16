<?php

/**
 * Created by PhpStorm.
 * User: daryl
 * Date: 2017/7/13
 * Time: 上午12:01
 */

use AtomSwoole\Foundation\Container;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function container_singleton()
    {
        $container = new Container;
        Container::setInstance($container);

        $this->assertSame($container, Container::getInstance());
        $this->assertInstanceOf(Container::class, $container);

        Container::setInstance();
        $this->assertNull(Container::getInstance());

        Container::setInstance(new Container);
        $this->assertNotSame($container, Container::getInstance());
    }
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

    /** @test */
    public function a_application_can_make_nested_dependencies()
    {
        $app = new \AtomSwoole\Foundation\Application();
        $app->singleton(PoiInterface::class, Poi::class);
        $app->singleton(MoeInterface::class, Moe::class);
        $app->singleton(YupInterface::class, Yup::class);
        $app->make(PoiInterface::class);
        $poi = $app[PoiInterface::class];
        $yup = $app[YupInterface::class];
        $this->assertEquals($yup, $poi->getMoe()->getYup());
    }

    /** @test */
    public function a_application_can_throw_an_exception_while_the_loop_dependency_appeared()
    {
        $this->expectException(\AtomSwoole\Exceptions\ContainerException::class);
        $app = new \AtomSwoole\Foundation\Application();
        $app->singleton(Loop::class);
        $app->singleton(Looped::class);
        $app->make(Loop::class);
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
    protected $yup;

    public function __construct(YupInterface $yup)
    {
        $this->yup = $yup;
    }

    public function getYup()
    {
        return $this->yup;
    }
}

interface YupInterface
{

}

class Yup implements YupInterface
{

}

class Loop
{
    protected $looped;

    public function __construct(Looped $looped)
    {
        $this->looped = $looped;
    }
}

class Looped
{
    protected $loop;

    public function __construct(Loop $loop)
    {
        $this->loop = $loop;
    }
}