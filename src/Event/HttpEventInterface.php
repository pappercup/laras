<?php

namespace Pappercup\Event;

interface HttpEventInterface {

    /**
     * reload 刷新文件用
     *
     * @param \Swoole\Http\Server $server
     * @return mixed
     * @author pappercup
     * @date 2018/9/12 13:57
     */
    public static function onWorkerStart(\Swoole\Http\Server $server);


    /**
     * request ===> response
     *
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     * @return mixed
     * @author pappercup
     * @date 2018/9/12 14:00
     */
    public static function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response);

}