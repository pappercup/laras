<?php

namespace Pappercup\Support;

use Illuminate\Support\Facades\Config;
use Pappercup\Config\Configure;
use Pappercup\Config\Environment;
use Pappercup\Core\SwooleHttp;
use Pappercup\Core\SwooleWebSocket;

trait ServerCommandTrait {

    use ProcessTrait;

    protected $server_map = [
        'http' => SwooleHttp::class,
        'websocket' => SwooleWebSocket::class,
    ];

    protected $actions = [ 'start', 'stop', 'reload', 'restart' ];

    public function __construct()
    {
        parent::__construct();
        if (!Configure::config($this->server_type)) {
            $this->print('configure failure', 'error');
            exit();
        }

    }

    public function check()
    {
        if (!Environment::check()){
            $this->print('environment not support: ', 'error');
            $error = Environment::getErrorMessage();
            if (!empty($error)) {
                foreach ($error as $key => $value) {
                    $this->print('    ' . $key . ' ===> ' . $value, 'error');
                }
            }
            exit();
        }
    }

    public function print($message, $option = 'info')
    {
        if ((int)Config::get('swoole.' . $this->server_type . '.options.daemonize') <= 0) {
            $this->$option($message);
        }
    }

    public function getPid()
    {
        return Configure::getPid();
    }

    public function deletePidFile()
    {
        return Configure::deletePidFile();
    }

    protected function start()
    {
        (app($this->server_map[$this->server_type]))->start();
    }

    protected function checkProcessRunning($pid, $delay) {
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


}