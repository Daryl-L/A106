<?php
/**
 * Created by PhpStorm.
 * User: daryl
 * Date: 2017/7/6
 * Time: 下午5:02
 */

namespace AtomSwoole\Foundation;

use Couchbase\Exception;
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
        unset($this->bindings);

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
     * @throws Exception
     */
    public function resolve($abstract)
    {
        if ($this->instances[$abstract]) {
            return $this->instances[$abstract];
        }

        $class = new ReflectionClass($abstract);
        $parameters = $class->getConstructor()->getParameters();
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $type = $parameter->getClass();

            $dependency = $type->getName();
            if (!isset($this->bindings[$dependency]) && !$parameter->allowsNull()) {
                throw new Exception("Cannot find {$dependency} bound.");
            }

            $dependency = $this->bindings[$dependency]['concrete'];
            if (isset($this->instances[$dependency])) {
                $dependencies[] = $this->instances[$dependency];
            } else {
                $class = new ReflectionClass($this->instances[$dependency]);
                $dependencies = $class->newInstance();
            }
        }
        $object = $class->newInstanceArgs($dependencies);

        if ($this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }
}