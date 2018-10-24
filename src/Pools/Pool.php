<?php

namespace Pappercup\Pools;

use Pappercup\Contracts\Pool\ContractPool;
use Pappercup\Exceptions\ResourceNotEnoughExceptions;

abstract class Pool implements ContractPool
{
    const MAX_LENGTH = 1000;

    private $available = true;
    private $pool = null;
    private $total = 0;
    protected $server = null;
    private $config = null;
    protected $size = 0;

    public function __construct(\swoole_server $server, $config)
    {
        $this->server = $server;
        $this->config = $config;
        $this->size = $this->computeMaxQueueLength();
        $this->initPool();
    }

    abstract function generator();

    private function initPool()
    {
        if (empty($this->pool)) {
            $this->pool = new \SplQueue();
        }
    }

    public function put($object)
    {
        if ($this->available && $this->count() < $this->size) {
            dump('---------------enqueue----------');
            ($this->pool)->enqueue($object);
        }
    }

    public function get($server)
    {
        if ($this->available && !$this->pool->isEmpty()) {
            dump('---------------dequeue----------');
            return $this->pool->dequeue();
        }
        if ($this->total <= $this->size) {
            $this->total++;
            return $this->generator($server);
        }else{
            throw new ResourceNotEnoughExceptions();
        }
    }

    public function __destruct()
    {
        $this->available = false;
        while (!$this->pool->isEmpty()) {
            dump('---------------destruct===dequeue----------');
            $connection = $this->pool->dequeue();
            $connection->disconnect();
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

    private function computeMaxQueueLength()
    {
        $worker_num = isset($this->server->setting['worker_num']) ? $this->server->setting['worker_num']: 4;
        $pool_size = isset($this->config['mysql']['size']) ? $this->config['mysql']['size']: 5;
        return min(floor(self::MAX_LENGTH / $worker_num), $pool_size);
    }

}