<?php

namespace Pappercup\Command;

use Pappercup\Core\CommandBridge;
use Pappercup\Core\SwooleHttp;

class SwooleHttpCommand extends CommandBridge
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:http {action : start|stop|reload|restart}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'bridge swoole and laravel; swoole:http start|stop|reload|restart tips: default is start: ex: swoole:http start: swoole:http stop: swoole:http;';

    protected $actions = [ 'start', 'stop', 'reload', 'restart' ];

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
        $this->check();
        $this->act();
    }

    protected function analysisArgument()
    {
        $action = $this->argument('action');
        if (empty($action)) {
            $action = 'start';
        }elseif (!in_array($action, $this->actions)) {
            $this->error('invalid action argument, must start|stop|reload|restart');
            exit();
        }
        return $action;
    }

    protected function act()
    {
        $action = $this->analysisArgument();
        $this->$action();
    }

    protected function start()
    {
        (app(SwooleHttp::class))->start();
    }

    protected function stop()
    {
        $pid = $this->getPid();
        if ((int)$pid > 0) {
            $this->killProcss($pid, SIGTERM);
            $res = $this->checkProcessRunning($pid, 15);
            if ($res) {
                $this->deletePidFile();
                $this->info('stopped...');
            }else {
                $this->error('error: can not stop swoole!!!');
            }
        }else{
            $this->info('swoole server is not running!');
        }
    }

    protected function reload()
    {
        $pid = $this->getPid();
        if ((int)$pid > 0) {
            $this->killProcss($pid, SIGUSR1);
            $this->info('reload...');
        }else{
            $this->info('swoole server is not running!');
        }
    }

    protected function restart()
    {
        $pid = $this->getPid();
        if ((int)$pid > 0) {
            $this->killProcss($pid, SIGTERM);
            $res = $this->checkProcessRunning($pid, 15);
            if ($res) {
                $this->deletePidFile();
                $this->info('restarted...');
                $this->start();
            }else {
                $this->error('failed...');
            }
        }else {
            $this->info('restarted...');
            $this->start();
        }
    }

    private function checkProcessRunning($pid, $delay) {
        $start_time = time();
        while (time() - $start_time < $delay) {
            if (!$this->isProcessRunning($pid)) {
                return true;
            }
            $this->info('check process running status: process still running...');
            sleep(1);
        }
        $this->error('waiting swoole stop timeout...');
        return false;
    }

}