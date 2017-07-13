<?php
/**
 * Created by PhpStorm.
 * User: daryl
 * Date: 2017/7/6
 * Time: 下午5:02
 */

namespace AtomSwoole\Foundation;

use AtomSwoole\Exceptions\ContainerException;
use ReflectionClass;

abstract class Container
{
    protected static $instance;

    protected $instances;

    protected $bindings;

    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * @param $abstract
     * @param null $concrete
     * @param bool $shared
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        unset($this->bindings[$abstract]);

        is_null($concrete) && $concrete = $abstract;

        $this->bindings[$abstract] = compact('concrete', 'shared');

        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $this->resolve($abstract);
        }
    }

    public function make($abstract)
    {
        return $this->resolve($abstract);
    }

    /**
     * @param $abstract
     * @param mixed $concrete
     * @return object
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * @param $abstract
     * @return object
     * @throws ContainerException
     */
    public function resolve($abstract)
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract]['concrete'];

        $class = new ReflectionClass($concrete);
        $parameters = $class->getConstructor()->getParameters();
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $type = $parameter->getClass();

            $dependency = $type->getName();
            if (!isset($this->bindings[$dependency]) && !$parameter->allowsNull()) {
                throw new ContainerException("Cannot find {$dependency} bound.");
            }

            if (isset($this->instances[$dependency])) {
                $dependencies[] = $this->instances[$dependency];
            } else {
                $dependencies[] = (new ReflectionClass($this->bindings[$dependency]['concrete']))->newInstanceArgs();
            }
        }
        $object = $class->newInstanceArgs($dependencies);

        if ($this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }
}