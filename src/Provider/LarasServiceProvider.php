<?php
/**
 * Created by PhpStorm.
 * User: duanbin
 * Date: 2018/9/12
 * Time: 11:23
 */

namespace Pappercup\Provider;

use Illuminate\Support\ServiceProvider;
use Pappercup\Command\SwooleHttp;
use Pappercup\Command\SwooleHttpCommand;
use Pappercup\Command\SwooleReload;
use Pappercup\Command\SwooleRestart;
use Pappercup\Command\SwooleStart;
use Pappercup\Command\SwooleStop;

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
            ]);
        }
        // 发布配置文件
        $this->publishes([
            __DIR__.'/../Config/swoole.php' => config_path('swoole.php'),
        ]);

    }


}