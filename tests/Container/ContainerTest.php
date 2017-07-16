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
    public function container_bind_and_make_with_dependencies()
    {
        $container = new Container();
        $container->bind(PoiInterface::class, Poi::class);
        $container->bind(MoeInterface::class, Moe::class);
        $container->bind(YupInterface::class, Yup::class);
        $this->assertInstanceOf(PoiInterface::class, $container->make(PoiInterface::class));
    }

    /** @test */
    public function container_bind_and_make_without_dependencies()
    {
        $container = new Container();
        $container->bind(YupInterface::class, Yup::class);
        $this->assertInstanceOf(YupInterface::class, $container->make(YupInterface::class));
    }

    /** @test */
    public function container_can_make_singleton_objects()
    {
        $container = new Container();
        $container->singleton(PoiInterface::class, Poi::class);
        $container->singleton(MoeInterface::class, Moe::class);
        $container->singleton(YupInterface::class, Yup::class);
        $moe = $container->make(MoeInterface::class);
        $poi = $container->make(PoiInterface::class);
        $this->assertSame($moe, $poi->getMoe());
    }

    /** @test */
    public function container_can_throw_an_exception_when_dependency_not_found()
    {
        $this->expectException(\AtomSwoole\Exceptions\ContainerException::class);
        $container = new Container();
        $container->singleton(PoiInterface::class, Poi::class);
        $container->make(PoiInterface::class);
    }

    /** @test */
    public function a_application_can_make_nested_dependencies()
    {
        $container = new Container();
        $container->singleton(PoiInterface::class, Poi::class);
        $container->singleton(MoeInterface::class, Moe::class);
        $container->singleton(YupInterface::class, Yup::class);
        $container->make(PoiInterface::class);
        $poi = $container[PoiInterface::class];
        $yup = $container[YupInterface::class];
        $this->assertEquals($yup, $poi->getMoe()->getYup());
    }

    /** @test */
    public function a_application_can_throw_an_exception_while_the_loop_dependency_appeared()
    {
        $this->expectException(\AtomSwoole\Exceptions\ContainerException::class);
        $container = new Container();
        $container->singleton(Loop::class);
        $container->singleton(Looped::class);
        $container->make(Loop::class);
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