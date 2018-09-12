<?php

namespace Pappercup\Event;

class HttpEvent implements HttpEventInterface
{

    public static function onWorkerStart(\Swoole\Http\Server $server)
    {

    }

    public static function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {

    }

}