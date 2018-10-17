<?php
/**
 * Created by PhpStorm.
 * User: pappercup
 * Date: 2018/9/29
 * Time: 16:19
 */

namespace Pappercup\Bridges;

use Pappercup\Contracts\Bridge\ContractSwooleMemory;
use Pappercup\Contracts\Event\ContractHttpEventCallback;
use Pappercup\Contracts\Event\ContractWebSocketEventCallback;
use Pappercup\Contracts\Pool\ContractPoolMySQL;
use Pappercup\Contracts\Server\ContractSwooleHttp;
use Pappercup\Contracts\Server\ContractSwooleWebSocket;
use Pappercup\Events\EventCallbackHttp;
use Pappercup\Events\EventCallbackWebSocket;
use Swoole\Server;
use Pappercup\Http\Request;
use Illuminate\Support\Facades\Config;
use Swoole\Http\Server as HttpServer;
use Swoole\WebSocket\Server as WebSocketServer;

class BridgeServer
{

    protected $app = null;
    protected $server = null;
    protected $memory = null;
    protected $pool = null;
    protected $extraEventCallback = null;

    protected $server_type = null;
    protected $server_map = [
        HttpServer::class => [
            'callback_contract' => ContractHttpEventCallback::class,
            'callback' => EventCallbackHttp::class,
            'server' => 'http',
            'server_contract' => ContractSwooleHttp::class,
        ],
        WebSocketServer::class => [
            'callback_contract' => ContractWebSocketEventCallback::class,
            'callback' => EventCallbackWebSocket::class,
            'server' => 'websocket',
            'server_contract' => ContractSwooleWebSocket::class,
        ],
    ];

    public function __construct(Server $server, array $memory)
    {
        $this->checkServerType($server);
        $this->server = $server;
        $this->memory = $memory;
        $this->extraEventCallback = $this->createApplication()->findExtraEventCallback();
    }

    private function checkServerType($server)
    {
        $this->server_type = $this->server_map[get_class($server)];
    }

    /**
     * 创建 laravel application
     *
     * @return $this
     * @author pappercup
     * @date 2018/9/18 15:53
     */
    protected function createApplication()
    {
        // create laravel app
        $this->app = require base_path() . '/bootstrap/app.php';
        // bind swoole http server
        $this->bindSwooleHttp();
        $this->bindSwooleMemory();
        $this->bindPoolMysql();
        return $this;
    }

    /**
     * 注册 一些额外的回调事件
     *
     * @return string
     * @author pappercup
     * @date 2018/9/18 15:53
     */
    protected function findExtraEventCallback()
    {
        $event_callback = Config::get('swoole.' . $this->server_type['server'] . '.event_callback');

        if (app($event_callback) instanceof  $this->server_type['callback_contract']) {
            return $event_callback;
        }else {
            return $this->server_type['callback'];
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
    protected function bindSwooleMemory()
    {
        // 注册 swoole http server
        $this->app->singleton(ContractSwooleMemory::class, function ($app) {
            return $this->memory;
        });
        // 绑定别名
        if (!$this->app->bound('swoole.memory')) {
            $this->app->alias(ContractSwooleMemory::class, 'swoole.memory');
        }
    }

    /**
     * @author pappercup
     * @date 2018/9/18 15:19
     */
    protected function bindSwooleHttp()
    {
        // 注册 swoole http server
        $this->app->singleton($this->server_type['server_contract'], function ($app) {
            return $this->server;
        });
        // 绑定别名
        if (!$this->app->bound('swoole.http')) {
            $this->app->alias($this->server_type['server_contract'], 'swoole.'. $this->server_type['server']);
        }
    }

    /**
     * @author pappercup
     * @date 2018/9/18 15:19
     */
    protected function bindPoolMysql()
    {
        // 注册 swoole http server
        $this->app->singleton(ContractPoolMySQL::class, function ($app) {
            return $this->server->pool[$this->server->worker_id];
        });
        // 绑定别名
        if (!$this->app->bound('pool.mysql')) {
            $this->app->alias(ContractPoolMySQL::class, 'pool.mysql');
        }
    }

    /**
     * @param $response
     * @param $swooleResponse
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 15:19
     */
    protected function responseMapper($response, $swooleResponse)
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