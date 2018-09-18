<?php
/**
 * Created by PhpStorm.
 * User: pappercup
 * Date: 2018/9/18
 * Time: 15:11
 */

namespace Pappercup\Core;

use Illuminate\Support\Facades\Config;
use Pappercup\Event\HttpEventCallback;
use Pappercup\Event\HttpEventCallbackContract;
use Swoole\Http\Server;
use Pappercup\Http\Request;

class HttpBridge
{

    private $app = null;
    private $server = null;
    private $extraEventCallback = null;

    public function __construct(Server $server)
    {
        $this->server = $server;
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
        $this->bindSwooleHttp($this->server);
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
        $event_callback = Config::get('swoole.http.event_callback');

        if (app($event_callback) instanceof HttpEventCallbackContract) {
            return $event_callback;
        }else {
            return HttpEventCallback::class;
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
        $this->extraEventCallback::beforeBootstrapLaravel($this->app);

        //index.php
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

        $response = $kernel->handle(
            $request = Request::captureSwooleRequest($swooleRequest)
        );
        $content = $response->getContent();
        $kernel->terminate($request, $response);

        $swooleResponse = $this->responseMapper($response, $swooleResponse);

        //  custom event callback
        $this->extraEventCallback::beforeBootstrapLaravel($this->app, $swooleResponse, $content);

        $swooleResponse->end($content);

    }

    /**
     * @param $swooleHttp
     * @author pappercup
     * @date 2018/9/18 15:19
     */
    private function bindSwooleHttp($swooleHttp)
    {
        // 注册 swoole http server
        $this->app->singleton(SwooleHttpContract::class, function ($app) use($swooleHttp) {
            return $swooleHttp;
        });
        // 绑定别名
        if (!$this->app->bound('swoole.http')) {
            $this->app->alias(SwooleHttpContract::class, 'swoole.http');
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