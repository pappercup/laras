<?php
/**
 * Created by PhpStorm.
 * User: pappercup
 * Date: 2018/9/18
 * Time: 13:23
 */

namespace Pappercup\Event;

use \Swoole\Server;

interface ContractServerEventCallback extends ContractEventCallback {

    /**
     * https://wiki.swoole.com/wiki/page/49.html
     *
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:37
     */
    public static function Connect(Server $server, int $fd, int $reactorId);

    /**
     * https://wiki.swoole.com/wiki/page/50.html
     *
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     * @param string $data
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:38
     */
    public static function Receive(Server $server, int $fd, int $reactorId, string $data);

}

