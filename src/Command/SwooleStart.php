<?php

namespace Pappercup\Command;

use Pappercup\Core\SwooleBridge;
use Pappercup\Http\Request;
use Illuminate\Support\Facades\Config;

class SwooleStart extends SwooleBridge
{
    protected $app = null;
    protected $http = null;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'started swoole in laravel';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->start();
    }

    public function initHttp()
    {
        $this->print('starting', 'info');

        $config = Config::get('swoole.http');

        $this->http = new \Swoole\Http\Server($config['host'], $config['port']);

        $this->http->set($config['options']);

        return $this;
    }

    public function start()
    {
        $this->initHttp();

        $this->http->on('WorkerStart', function ($server) {
            require_once __DIR__.'/../../../vendor/autoload.php';
            $this->app = require __DIR__.'/../../../bootstrap/app.php';
            // 记录pid pid_file
            $this->storePid($server->master_pid);
        });

        $this->http->on('request', function ($request, $response) {
            $this->bootLaravel($request, $response);
        });
        $this->http->start();
    }

    public function bootLaravel($swooleRequest, $swooleResponse)
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