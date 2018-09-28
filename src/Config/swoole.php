<?php
/**
 * referer https://wiki.swoole.com/wiki/page/13.html
 * 参考官网配置 https://wiki.swoole.com/wiki/page/274.html
 *
 * User: pappercup
 * Date: 2018/9/7
 * Time: 17:10
 */

return [
    'http' => [
        'pid_file_path' => storage_path('swoole/http'),
        'host' => env('SWOOLE_HTTP_HOST', '0.0.0.0'),
        'port' => env('SWOOLE_HTTP_PORT', 7000),
        'options' => [
            'daemonize' => 1,   // 是否以守护进程方式启动
            'log_file' => storage_path('logs/swoole.log'),

            'dispatch_mode' => 1,   // 1平均分配，2按FD取模固定分配，3抢占式分配

            // 心跳检测, heartbeat_idle_time 必须大于或等于 heartbeat_check_interval
            'heartbeat_check_interval' => 30,   // 心跳检测间隔
            'heartbeat_idle_time' => 60,    // 闲置时间, 超过次时间会关闭

            'max_request' => 2000, // 当前 worker 进程处理完 n 次后请求后结束运行

            'worker_num' => 4, // worker 进程数量, 同步可以设置的大些, 异步的话 设置为 cpu 核数的 1~4倍即可

            'reactor_num' => 2, // 通过此参数来调节poll线程的数量，以充分利用多核, cpu 核数

            'max_conn' => 10000,    // 设置Server最大允许维持多少个tcp连接, 超过此数量后，新进入的连接将被拒绝

//            'upload_tmp_dir' => '/data/uploadfiles/',   // 上传文件临时目录
//            'http_parse_post' => false,     // https://wiki.swoole.com/wiki/page/p-http_parse_post.html
//            'http_compression' => true,
//
//            // https://wiki.swoole.com/wiki/page/783.html
//            'document_root' => '/data/webroot/example.com',
//            'enable_static_handler' => true,
        ],
//        'event_callback' => Pappercup\Event\HttpEventCallback::class,   // default http core callback
        'event_callback' => null,   // 事件回调 类必须实现 Pappercup\Event\HttpEventCallbackContract;

        /**
         * This may cause memory leaks.
         */
        'memory' => [
            'table' => false,
//            'table' => [
//                'size' => 1024,
//                'conflict_proportion' => 0.2,
//            ],
            'atomic' => [
                'init_value' => 0,
            ],
        ],
    ],

    'webSocket' => [
        'pid_file_path' => storage_path('swoole/web_socket'),
        'host' => env('SWOOLE_HTTP_HOST', '0.0.0.0'),
        'port' => env('SWOOLE_HTTP_PORT', 7001),
        'options' => [
            'daemonize' => 1,   // 是否以守护进程方式启动
            'log_file' => storage_path('logs/swoole.log'),

            'dispatch_mode' => 1,   // 1平均分配，2按FD取模固定分配，3抢占式分配

            // 心跳检测, heartbeat_idle_time 必须大于或等于 heartbeat_check_interval
            'heartbeat_check_interval' => 30,   // 心跳检测间隔
            'heartbeat_idle_time' => 60,    // 闲置时间, 超过次时间会关闭

            'max_request' => 2000, // 当前 worker 进程处理完 n 次后请求后结束运行

            'worker_num' => 4, // worker 进程数量, 同步可以设置的大些, 异步的话 设置为 cpu 核数的 1~4倍即可

            'reactor_num' => 2, // 通过此参数来调节poll线程的数量，以充分利用多核, cpu 核数

            'max_conn' => 10000,    // 设置Server最大允许维持多少个tcp连接, 超过此数量后，新进入的连接将被拒绝

//            'upload_tmp_dir' => '/data/uploadfiles/',   // 上传文件临时目录
//            'http_parse_post' => false,     // https://wiki.swoole.com/wiki/page/p-http_parse_post.html
//            'http_compression' => true,
//
//            // https://wiki.swoole.com/wiki/page/783.html
//            'document_root' => '/data/webroot/example.com',
//            'enable_static_handler' => true,

            // webSocket config https://wiki.swoole.com/wiki/page/825.html
//            'websocket_subprotocol' => 'chat',
            "open_websocket_close_frame" => true,

        ],
//        'event_callback' => Pappercup\Event\HttpEventCallback::class,   // default http core callback
        'event_callback' => null,   // 事件回调 类必须实现 Pappercup\Event\HttpEventCallbackContract;

        /**
         * This may cause memory leaks.
         */
        'memory' => [
            'table' => false,
//            'table' => [
//                'size' => 1024,
//                'conflict_proportion' => 0.2,
//            ],
            'atomic' => [
                'init_value' => 0,
            ],
        ],
    ],



];