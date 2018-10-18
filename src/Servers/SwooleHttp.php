<?php

namespace Pappercup\Servers;

use Pappercup\Bridges\BridgeHttp;
use Pappercup\Bridges\BridgeMemory;
use Pappercup\Contracts\Event\ContractHttpEventCallback;
use Pappercup\Contracts\Server\ContractSwooleHttp;
use Pappercup\Events\EventCallbackHttp;
use Illuminate\Support\Facades\Config;
use Pappercup\Servers\Expends\Http;

class SwooleHttp implements ContractSwooleHttp
{

    private $http = null;
    private $bridge = null;
    private $config = [];
    private $memory = [];
    protected $server_type = 'http';

    // http 注册的回调事件,  注意 没有 request 事件, 因为 在 想在 request 中实例化 laravel app 并将 swoole http 对象绑定其中
    private $eventList = [
        'Start', 'Shutdown', 'WorkerStart', 'WorkerStop', 'WorkerExit', 'Packet', 'Close', 'BufferFull', 'BufferEmpty',
        'Task', 'Finish', 'PipeMessage', 'WorkerError', 'ManagerStart', 'ManagerStop',
    ];

    public function __construct()
    {
        $this->config = Config::get('swoole.' . $this->server_type);
        $this->createSwooleMemory();
    }

    private function initHttp()
    {
        if ( !($this->http instanceof Http) ) {
            $this->http = new Http($this->config['host'], $this->config['port']);
            $this->http->set($this->config['options']);
            // 注册 事件回调
            if ( isset($this->config['event_callback']) && app($this->config['event_callback']) instanceof ContractHttpEventCallback) {
                $this->bindHttpEventCallback($this->config['event_callback']);
            }else {
                $this->bindDefaultHttpEventCallback();
            }
        }
        $this->http->config = config();
        $this->bridge = new BridgeHttp($this->http, $this->memory);
        return $this;
    }

    public function getHttp()
    {
        return $this->http;
    }

    private function createSwooleMemory()
    {
        !isset($this->config['memory']) ?: $this->memory = BridgeMemory::createSwooleMemory($this->config['memory']);
    }

    /**
     * 绑定回调事件
     *
     * @param string $httpEventCallback
     * @author pappercup
     * @date 2018/9/18 15:00
     */
    private function bindHttpEventCallback(string $httpEventCallback)
    {
        $methods = get_class_methods($httpEventCallback);
        foreach ($this->eventList as $callback) {
            if (in_array($callback, $methods)) {
                $this->http->on($callback, [ $httpEventCallback, $callback ]);
            }
        }
    }

    /**
     * 绑定默认的事件回调
     *
     * @author pappercup
     * @date 2018/9/18 15:07
     */
    private function bindDefaultHttpEventCallback()
    {
        $this->bindHttpEventCallback(EventCallbackHttp::class);
    }


    /**
     * 绑定两个特殊的监听事件
     *
     * @return mixed
     * @author pappercup
     * @date 2018/9/13 17:52
     */
    public function start()
    {
        return $this->initHttp()->onRequest()->getHttp()->start();
    }

    /**
     * 监听特殊的方法
     *
     * @author pappercup
     * @date 2018/9/18 15:38
     */
    private function onRequest()
    {
        $this->http->on('Request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
            $this->bridge->createApplication()->bootstrapLaravel($request, $response);
        });
        return $this;
    }



}