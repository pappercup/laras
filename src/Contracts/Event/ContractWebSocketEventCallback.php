<?php
/**
 * Created by PhpStorm.
 * User: pappercup
 * Date: 2018/9/18
 * Time: 13:23
 */

namespace Pappercup\Contracts\Event;


interface ContractWebSocketEventCallback extends ContractHttpEventCallback {

    /**
     * https://wiki.swoole.com/wiki/page/409.html
     *
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:54
     */
//    public static function HandShake(\Swoole\Http\Request $request, \Swoole\Http\Response $response);

    /**
     * https://wiki.swoole.com/wiki/page/401.html
     *
     * @param \Swoole\WebSocket\Server $server
     * @param \Swoole\Http\Request $request
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:56
     */
    public static function Open(\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request);

    /**
     * https://wiki.swoole.com/wiki/page/402.html
     *
     * @param \Swoole\WebSocket\Server $server
     * @param \Swoole\WebSocket\Frame $frame
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:57
     */
    public static function Message(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame);

}

