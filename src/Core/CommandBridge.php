<?php

namespace Pappercup\Core;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Pappercup\Config\Configure;
use Pappercup\Support\ProcessTrait;

class CommandBridge extends Command
{
    use ProcessTrait;

    public function __construct()
    {
        parent::__construct();

        if (!Configure::config()) {
            $this->print('configure failure', 'error');
            return false;
        }
    }

    public function print($message, $option = 'info')
    {
        if (!Config::get('swoole.http.options.daemonize')) {
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