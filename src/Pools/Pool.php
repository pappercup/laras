<?php

namespace Pappercup\Pools;


use Pappercup\Contracts\Pool\ContractPool;

abstract class Pool implements ContractPool
{
    private static $instance = null;
    private static $available = true;
    private static $pool = null;

    private function __construct(){}

    abstract static function generator($server) : Object;

    public static function instance()
    {
        if (empty(self::$instance)) {
            return (new static)->initPool();
        }
        return self::$instance;
    }

    protected function initPool()
    {
        if (empty(self::$pool)) {
            self::$pool = new \SplQueue();
        }
        return $this;
    }

    public static function _put(Object $object)
    {
        if (self::$available) {
            (self::$pool)->enqueue($object);
        }
    }

    public function put(Object $object)
    {
        if (self::$available) {
            (self::$pool)->enqueue($object);
        }
    }

    public function get($server)
    {
        if (self::$available && !self::$pool->isEmpty()) {
            return self::$pool->dequeue();
        }

        return $this->generator($server);
    }

    public function __destruct()
    {
        self::$available = false;
        while (!self::$pool->isEmpty()) {
            self::$pool->dequeue();
        }
    }

    public function destruct()
    {
        $this->__destruct();
    }

    public function pool()
    {
        return self::$pool;
    }

    public function count()
    {
        return self::$pool->count();
    }

    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array([self::class, $method], $parameters);
    }

}