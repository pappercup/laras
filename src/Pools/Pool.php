<?php

namespace Pappercup\Pools;


use Pappercup\Contracts\Pool\ContractPool;

abstract class Pool implements ContractPool
{
    private $available = true;
    private $pool = null;

    public function __construct()
    {
        $this->initPool();
    }

    abstract function generator($server) : Object;

    private function initPool()
    {
        if (empty($this->pool)) {
            $this->pool = new \SplQueue();
        }
    }

    public function _put(Object $object)
    {
        if ($this->available) {
            ($this->pool)->enqueue($object);
        }
    }

    public function put(Object $object)
    {
        if ($this->available) {
            ($this->pool)->enqueue($object);
        }
    }

    public function get($server)
    {
        if ($this->available && !$this->pool->isEmpty()) {
            return $this->pool->dequeue();
        }

        return $this->generator($server);
    }

    public function __destruct()
    {
        $this->available = false;
        while (!$this->pool->isEmpty()) {
            $this->pool->dequeue();
        }
    }

    public function destruct()
    {
        $this->__destruct();
    }

    public function pool()
    {
        return $this->pool;
    }

    public function count()
    {
        return $this->pool->count();
    }


}