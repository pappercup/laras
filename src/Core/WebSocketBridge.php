<?php
/**
 * Created by PhpStorm.
 * User: pappercup
 * Date: 2018/9/18
 * Time: 15:11
 */

namespace Pappercup\Core;

use Illuminate\Support\Facades\Config;
use Pappercup\Event\WebSocketEventCallback;
use Pappercup\Event\WebSocketEventCallbackContract;
use Swoole\WebSocket\Server;
use Pappercup\Http\Request;

class WebSocketBridge
{

    private $app = null;
    private $server = null;
    private $memory = null;
    private $extraEventCallback = null;

    public function __construct(Server $server, array $memory)
    {
        $this->server = $server;
        $this->memory = $memory;
        $this->extraEventCallback = $this->createApplication()->findExtraEventCallback();
    }

    /**
     * 创建 laravel application
     *
     * @return $this
     * @author pappercup
     * @date 2018/9/18 15:53
     */
    private function createApplication()
    {
        // create laravel app
        $this->app = require base_path() . '/bootstrap/app.php';
        // bind swoole http server
        $this->bindSwooleWebSocket();
        $this->bindSwooleMemory();
        return $this;
    }

    /**
     * 注册 一些额外的回调事件
     *
     * @return string
     * @author pappercup
     * @date 2018/9/18 15:53
     */
    private function findExtraEventCallback()
    {
        $event_callback = Config::get('swoole.webSocket.event_callback');

        if (app($event_callback) instanceof WebSocketEventCallbackContract) {
            return $event_callback;
        }else {
            return WebSocketEventCallback::class;
        }
    }

    /**
     * @param $swooleRequest
     * @param $swooleResponse
     * @author pappercup
     * @date 2018/9/18 15:19
     */
    public function bootstrapLaravel($swooleRequest, $swooleResponse)
    {
        //  custom event callback
        $this->extraEventCallback::beforeRunLaravel($this->app);

        //index.php
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

        $response = $kernel->handle(
            $request = Request::captureSwooleRequest($swooleRequest)
        );
        $content = $response->getContent();
        $kernel->terminate($request, $response);

        $swooleResponse = $this->responseMapper($response, $swooleResponse);

        //  custom event callback
        $this->extraEventCallback::afterRunLaravel($this->app, $swooleResponse, $content);

        $swooleResponse->end($content);

    }

    /**
     * @author pappercup
     * @date 2018/9/18 17:32
     */
    private function bindSwooleMemory()
    {
        // 注册 swoole http server
        $this->app->singleton(SwooleMemoryContract::class, function ($app) {
            return $this->memory;
        });
        // 绑定别名
        if (!$this->app->bound('swoole.memory')) {
            $this->app->alias(SwooleMemoryContract::class, 'swoole.memory');
        }
    }

    /**
     * @author pappercup
     * @date 2018/9/18 15:19
     */
    private function bindSwooleWebSocket()
    {
        // 注册 swoole http server
        $this->app->singleton(SwooleWebSocketContract::class, function ($app) {
            return $this->server;
        });
        // 绑定别名
        if (!$this->app->bound('swoole.webSocket')) {
            $this->app->alias(SwooleWebSocketContract::class, 'swoole.webSocket');
        }
    }

    /**
     * @param $response
     * @param $swooleResponse
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 15:19
     */
    private function responseMapper($response, $swooleResponse)
    {
        foreach ($response->headers as $key => $value) {
            $swooleResponse->header($key, $value[0]);
        }

        $swooleResponse->status($response->getStatusCode());

        foreach ($response->headers->getCookies() as $cookie) {
            $swooleResponse->cookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
        }
        return $swooleResponse;
    }




}