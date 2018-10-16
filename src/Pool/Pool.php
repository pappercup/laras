<?php

namespace Pappercup\Pool;


abstract class Pool implements ContractPool
{

    private static $available = true;
    private static $pool = null;

    public function __construct()
    {
        self::$pool = new \SplQueue();
    }

    abstract static function generator() : Object;

    public static function put(Object $object)
    {
        if (self::$available) {
            (self::$pool)->enqueue($object);
        }
    }

    public static function get()
    {
        if (self::$available && !self::$pool->isEmpty()) {
            return self::$pool->dequeue();
        }

        return self::generator();

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

}