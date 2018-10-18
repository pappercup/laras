<?php
/**
 * Created by PhpStorm.
 * User: duanbin
 * Date: 2018/9/12
 * Time: 11:23
 */

namespace Pappercup\Provider;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Pappercup\Commands\SwooleHttpCommand;
use Pappercup\Commands\SwooleWebSocketCommand;

class LarasServiceProvider extends ServiceProvider
{

    public function register()
    {
        // 合并配置文件
        $this->mergeConfigFrom(
            __DIR__.'/../Config/swoole.php', 'swoole'
        );
    }


    public function boot()
    {
        // 注册命令行工具
        if ($this->app->runningInConsole()) {
            $this->commands([
                SwooleHttpCommand::class,
                SwooleWebSocketCommand::class,
            ]);
        }
        // 发布配置文件
        $this->publishes([
            __DIR__.'/../Config/swoole.php' => config_path('swoole.php'),
        ]);

        DB::extend('coroutine.mysql', function ($config, $name) {
            return $this->makeCoroutineMysqlConnection($config, $name);
        });

    }


    public function makeCoroutineMysqlConnection($config, $name)
    {
        return app()['pool.mysql']->get(app()['swoole.http']);
    }


}