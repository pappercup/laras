<?php

namespace Pappercup\Event;

use Illuminate\Foundation\Application;
use Pappercup\Config\Configure;
use Swoole\Server;

class EventCallbackHttp implements ContractHttpEventCallback
{
    /**
     * https://wiki.swoole.com/wiki/page/p-event/onStart.html
     *
     * 可以在onStart回调中，将$serv->master_pid和$serv->manager_pid的值保存到一个文件中
     *
     * @param Server $server
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:28
     */
    public static function Start(Server $server)
    {
        Configure::storePid($server->master_pid);
    }

    /**
     * https://wiki.swoole.com/wiki/page/p-event/onShutdown.html
     *
     * @param Server $server
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:31
     */
    public static function Shutdown(Server $server)
    {
        // TODO: Implement Shutdown() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/p-event/onWorkerStart.html
     *
     * reload 刷新文件用
     *
     * @param Server $server
     * @param int $worker_id
     * @return mixed
     * @author pappercup
     * @date 2018/9/12 13:57
     */
    public static function WorkerStart(Server $server, int $worker_id)
    {
        // TODO: Implement WorkerStart() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/p-event/onWorkerStop.html
     *
     * @param Server $server
     * @param int $worker_id
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:34
     */
    public static function WorkerStop(Server $server, int $worker_id)
    {
        // TODO: Implement WorkerStop() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/808.html
     *
     * 仅在开启reload_async特性后有效。异步重启特性，会先创建新的Worker进程处理新请求，旧的Worker进程自行退出
     *
     * @param Server $server
     * @param int $worker_id
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:35
     */
    public static function WorkerExit(Server $server, int $worker_id)
    {
        // TODO: Implement WorkerExit() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/450.html
     *
     * @param Server $server
     * @param string $data
     * @param array $client_info
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:38
     */
    public static function Packet(Server $server, string $data, array $client_info)
    {
        // TODO: Implement Packet() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/p-event/onClose.html
     *
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:40
     */
    public static function Close(Server $server, int $fd, int $reactorId)
    {
        // TODO: Implement Close() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/745.html
     *
     * @param Server $server
     * @param int $fd
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:41
     */
    public static function BufferFull(Server $server, int $fd)
    {
        // TODO: Implement BufferFull() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/746.html
     *
     * @param Server $server
     * @param int $fd
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:41
     */
    public static function BufferEmpty(Server $server, int $fd)
    {
        // TODO: Implement BufferEmpty() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/54.html
     *
     * @param Server $server
     * @param int $task_id
     * @param int $src_worker_id
     * @param $data
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:44
     */
    public static function Task(Server $server, int $task_id, int $src_worker_id, $data)
    {
        // TODO: Implement Task() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/136.html
     *
     * @param Server $server
     * @param int $task_id
     * @param string $data
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:44
     */
    public static function Finish(Server $server, int $task_id, string $data)
    {
        // TODO: Implement Finish() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/366.html
     *
     * @param Server $server
     * @param int $src_worker_id
     * @param $message
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:45
     */
    public static function PipeMessage(Server $server, int $src_worker_id, $message)
    {
        // TODO: Implement PipeMessage() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/166.html
     *
     * @param Server $server
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @param int $signal
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:46
     */
    public static function WorkerError(Server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal)
    {
        // TODO: Implement WorkerError() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/190.html
     *
     * @param Server $server
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:47
     */
    public static function ManagerStart(Server $server)
    {
        // TODO: Implement ManagerStart() method.
    }

    /**
     * https://wiki.swoole.com/wiki/page/191.html
     *
     * @param Server $server
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 13:47
     */
    public static function ManagerStop(Server $server)
    {
        // TODO: Implement ManagerStop() method.
    }

    /**
     * @param Application $application
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 15:44
     */
    public static function beforeRunLaravel(Application $application)
    {
        // TODO: Implement beforeRunLaravel() method.
        dump('before run laravel');
    }

    /**
     * @param Application $application
     * @param \Swoole\Http\Response $swooleResponse
     * @param string $content
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 15:44
     */
    public static function afterRunLaravel(Application $application, \Swoole\Http\Response $swooleResponse, string $content)
    {
        // TODO: Implement afterRunLaravel() method.
        dump('after run laravel');
    }


}