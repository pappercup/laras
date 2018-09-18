<?php

namespace Pappercup\Core;

use Illuminate\Support\Facades\Config;
use Pappercup\Event\HttpEventCallback;
use Pappercup\Event\HttpEventCallbackContract;
use \Swoole\Http\Server;

class SwooleHttp implements SwooleHttpContract
{

    private $http = null;
    // http 注册的回调事件,  注意 没有 request 事件, 因为 在 想在 request 中实例化 laravel app 并将 swoole http 对象绑定其中
    private $eventList = [
        'Start', 'Shutdown', 'WorkerStart', 'WorkerStop', 'WorkerExit', 'Packet', 'Close', 'BufferFull', 'BufferEmpty',
        'Task', 'Finish', 'PipeMessage', 'WorkerError', 'ManagerStart', 'ManagerStop',
    ];

    public function __construct()
    {
        $this->initHttp();
    }

    public function initHttp()
    {
        if ( !($this->http instanceof Server) ) {
            $config = Config::get('swoole.http');
            $this->http = new Server($config['host'], $config['port']);
            $this->http->set($config['options']);
            // 注册 事件回调
            if ( isset($config['event_callback']) && app($config['event_callback']) instanceof HttpEventCallbackContract) {
                $this->bindHttpEventCallback($config['event_callback']);
            }else {
                $this->bindDefaultHttpEventCallback();
            }
        }
    }

    public function getHttp()
    {
        return $this->http;
    }

    /**
     * 绑定回调事件
     *
     * @param string $httpEventCallback
     * @author pappercup
     * @date 2018/9/18 15:00
     */
    public function bindHttpEventCallback(string $httpEventCallback)
    {
        foreach ($this->eventList as $callback) {
            $this->http->on($callback, [ $httpEventCallback, $callback ]);
        }
    }

    /**
     * 绑定默认的事件回调
     *
     * @author pappercup
     * @date 2018/9/18 15:07
     */
    public function bindDefaultHttpEventCallback()
    {
        $this->bindHttpEventCallback(HttpEventCallback::class);
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
        return $this->onRequest()->http->start();
    }

    /**
     * 监听特殊的方法
     *
     * @author pappercup
     * @date 2018/9/18 15:38
     */
    public function onRequest()
    {
        $this->http->on('Request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
            (new HttpBridge($this->http))->bootstrapLaravel($request, $response);
        });
        return $this;
    }



}