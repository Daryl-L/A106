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
use ArrayAccess;

abstract class Container implements ArrayAccess
{
    protected static $instance;

    protected $instances;

    protected $bindings;

    protected $buildStack;

    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Bind the abstract to concrete.
     *
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

    /**
     * Make a instance.
     *
     * @param $abstract
     * @return object
     */
    public function make($abstract)
    {
        return $this->resolve($abstract);
    }

    /**
     * Bind abstract to concrete as a single instance.
     *
     * @param $abstract
     * @param mixed $concrete
     * @return object
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * resolve the concrete from bindings.
     *
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

        $object = $this->build($concrete);

        if ($this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Build the instance if nested exists.
     *
     * @param $concrete
     * @return object
     * @throws ContainerException
     */
    protected function build($concrete)
    {
        $this->buildStack[] = $concrete;

        $class = new ReflectionClass($concrete);

        $constructor = $class->getConstructor();
        if (is_null($constructor)) {
            return $class->newInstanceWithoutConstructor();
        }

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
            } elseif (in_array($this->bindings[$dependency]['concrete'], $this->buildStack)) {
                throw new ContainerException("The loop dependency appeared in {$concrete}.");
            } else {
                $instance = $this->build($this->bindings[$dependency]['concrete']);
                if ($this->bindings[$dependency]['shared']) {
                    $this->instances[$dependency] = $instance;
                }
                $dependencies[] = $instance;
            }
        }
        array_pop($this->buildStack);
        return $class->newInstanceArgs($dependencies);
    }

    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($offset)
    {
        return $this->instances[$offset];
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }
}