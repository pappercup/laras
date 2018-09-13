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

    public function start()
    {
        $this->onWorkerStart()->onRequest();
        return $this->http->start();
    }

    public function onWorkerStart()
    {
        $this->http->on('WorkerStart', function ($server) {
            require_once base_path().'/vendor/autoload.php';
            $this->app = require base_path() . '/bootstrap/app.php';
            // 记录pid pid_file
            Configure::storePid($server->master_pid);

            // 注册 swoole http server
            $this->app->singleton(SwooleHttpContract::class, function ($app) use($server) {
                return $server;
            });
            // 绑定别名
            if (!$this->app->bound('swoole.http')) {
                $this->app->alias(SwooleHttpContract::class, 'swoole.http');
            }

        });
        return $this;
    }

    public function onRequest()
    {
        $this->http->on('request', function ($request, $response) {
            $this->bootLaravel($request, $response);
        });
        return $this;
    }

    protected function bootLaravel($swooleRequest, $swooleResponse)
    {
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

        $response = $kernel->handle(
            $request = Request::captureSwooleRequest($swooleRequest)
        );
        $content = $response->getContent();
        $kernel->terminate($request, $response);

        $this->responseMapper($response, $swooleResponse)->end($content);
    }

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