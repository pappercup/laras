<?php

namespace Pappercup\Core;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Pappercup\Config\Configure;
use Pappercup\Config\Environment;
use Pappercup\Support\ProcessTrait;

class CommandBridge extends Command
{
    use ProcessTrait;

    protected $server_type = null;

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


}