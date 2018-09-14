<?php

namespace Pappercup\Core;

use Illuminate\Support\Facades\Config;
use \Swoole\Http\Server;
use Pappercup\Http\Request;
use Pappercup\Config\Configure;

class SwooleHttp implements SwooleHttpContract
{

    private $http = null;
    private $app = null;

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
        }
    }

    public function getHttp()
    {
        return $this->http;
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
        $this->onWorkerStart()->onRequest();
        return $this->http->start();
    }

    /**
     * request 监听事件
     *
     * @return $this
     * @author pappercup
     * @date 2018/9/13 17:52
     */
    public function onRequest()
    {
        $server = $this->http;
        $this->http->on('request', function ($request, $response) use($server) {
            $this->bootLaravel($request, $response, $server);
        });
        return $this;
    }

    /**
     * worker start 监听事件
     *
     * @return $this
     * @author pappercup
     * @date 2018/9/13 17:52
     */
    public function onWorkerStart()
    {
        $this->http->on('WorkerStart', function ($server) {
            // 记录pid pid_file
            Configure::storePid($server->master_pid);
        });
        return $this;
    }

    /**
     * 将 swoole http 绑定到 laravel 容器中
     *
     * @param $app
     * @param $swooleHttp
     * @author pappercup
     * @date 2018/9/13 18:08
     */
    private function bindSwooleHttp($app, $swooleHttp)
    {
        // 注册 swoole http server
        $app->singleton(SwooleHttpContract::class, function ($app) use($swooleHttp) {
            return $swooleHttp;
        });
        // 绑定别名
        if (!$app->bound('swoole.http')) {
            $app->alias(SwooleHttpContract::class, 'swoole.http');
        }
    }

    /**
     * 启动 laravel
     *
     * @param $swooleRequest
     * @param $swooleResponse
     * @author pappercup
     * @date 2018/9/13 18:09
     */
    protected function bootLaravel($swooleRequest, $swooleResponse, $server)
    {
        // create laravel app
        $this->app = require base_path() . '/bootstrap/app.php';
        // bind swoole http server
        $this->bindSwooleHttp($this->app, $server);
        // now  is  index.php
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

        $response = $kernel->handle(
            $request = Request::captureSwooleRequest($swooleRequest)
        );
        $content = $response->getContent();
        $kernel->terminate($request, $response);

        $this->responseMapper($response, $swooleResponse)->end($content);
    }

    /**
     * laravel-response map to swoole-response
     *
     * @param $response
     * @param $swooleResponse
     * @return mixed
     * @author pappercup
     * @date 2018/9/13 18:09
     */
    public function responseMapper($response, $swooleResponse)
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