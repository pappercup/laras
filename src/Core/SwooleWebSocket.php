<?php

namespace Pappercup\Core;

use Illuminate\Support\Facades\Config;
use Pappercup\Event\WebSocketEventCallback;
use Pappercup\Event\WebSocketEventCallbackContract;
use \Swoole\WebSocket\Server;

class SwooleWebSocket  implements SwooleWebSocketContract
{

    private $webSocket = null;
    private $config = [];
    private $memory = [];

    // http 注册的回调事件,  注意 没有 request 事件, 因为 在 想在 request 中实例化 laravel app 并将 swoole http 对象绑定其中
    private $eventList = [
        'Start', 'Shutdown', 'WorkerStart', 'WorkerStop', 'WorkerExit', 'Packet', 'Close', 'BufferFull', 'BufferEmpty',
        'Task', 'Finish', 'PipeMessage', 'WorkerError', 'ManagerStart', 'ManagerStop', 'HandShake', 'Open', 'Message'
    ];

    public function __construct()
    {
        $this->config = Config::get('swoole.webSocket');
        $this->createSwooleMemory();
    }

    private function initWebSocket()
    {
        if ( !($this->webSocket instanceof Server) ) {
            $this->webSocket = new Server($this->config['host'], $this->config['port']);
            $this->webSocket->set($this->config['options']);
            // 注册 事件回调
            if ( isset($this->config['event_callback']) && app($this->config['event_callback']) instanceof WebSocketEventCallbackContract) {
                $this->bindWebSocketEventCallback($this->config['event_callback']);
            }else {
                $this->bindDefaultWebSocketEventCallback();
            }
        }
        return $this;
    }

    public function getWebSocket()
    {
        return $this->webSocket;
    }

    private function createSwooleMemory()
    {
        !isset($this->config['memory']) ?: $this->memory = MemoryBridge::createSwooleMemory($this->config['memory']);
    }

    /**
     * 绑定回调事件
     *
     * @param string $httpEventCallback
     * @author pappercup
     * @date 2018/9/18 15:00
     */
    private function bindWebSocketEventCallback(string $httpEventCallback)
    {
        $methods = get_class_methods($httpEventCallback);
        foreach ($this->eventList as $callback) {
            if (in_array($callback, $methods)) {
                $this->webSocket->on($callback, [ $httpEventCallback, $callback ]);
            }
        }
    }

    /**
     * 绑定默认的事件回调
     *
     * @author pappercup
     * @date 2018/9/18 15:07
     */
    private function bindDefaultWebSocketEventCallback()
    {
        $this->bindWebSocketEventCallback(WebSocketEventCallback::class);
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
        return $this->initWebSocket()->onRequest()->getWebSocket()->start();
    }

    /**
     * 监听特殊的方法
     *
     * @author pappercup
     * @date 2018/9/18 15:38
     */
    private function onRequest()
    {
        $this->webSocket->on('Request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
            (new WebSocketBridge($this->webSocket, $this->memory))->bootstrapLaravel($request, $response);
        });
        return $this;
    }



}