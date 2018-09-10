<?php

namespace Pappercup\Core;

use Illuminate\Console\Command;
use Swoole\Process;
use Illuminate\Support\Facades\Config;

class SwooleBridge extends Command
{

    private $pid_file = null;
    private $pid_file_name = 'swoole.pid';

    public function __construct()
    {
        parent::__construct();

        if (!$this->configure()) {
            $this->print('configure failure', 'error');
            return false;
        }
    }

    public function configure()
    {
        $res = true;

        $pid_file = rtrim(Config::get('swoole.http.pid_file_path'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->pid_file_name;
        $this->pid_file = $pid_file;
        if (!file_exists($pid_file)) {
            $res = $this->createPidFile($pid_file);
        }

        $log_file = Config::get('swoole.http.options.log_file');
        if (!file_exists($log_file)) {
            $res = $this->createLogFile($log_file);
        }
        return $res;
    }

    protected function createLogFile($file)
    {
        return $this->createFile($file);
    }

    protected function createPidFile($pid_file)
    {
        return $this->createFile($pid_file);
    }

    private function createFile($file)
    {
        return !is_file($file) ? (function ($file) {
            $dir = dirname($file);
            return is_dir($dir) ?: mkdir($dir, 0755, true) ? touch($file) ? true: false: false;
        })($file): true;
    }

    protected function killProcss($pid, $signo)
    {
        return Process::kill($pid, $signo);
    }

    public function getPid()
    {
        return file_get_contents($this->pid_file);
    }

    public function storePid($pid)
    {
        return file_put_contents($this->getPidFile(), $pid);
    }

    public function getPidFile()
    {
        return $this->pid_file;
    }

    public function deletePidFile()
    {
        unlink($this->getPidFile());
    }


    public function print($message, $option = 'info')
    {
        if (!Config::get('swoole.http.options.daemonize')) {
            $this->$option($message);
        }
    }

}