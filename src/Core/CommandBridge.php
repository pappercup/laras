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

    public function __construct()
    {
        parent::__construct();
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
            return false;
        }

        if (!Configure::config()) {
            $this->print('configure failure', 'error');
            return false;
        }
        return true;
    }

    public function print($message, $option = 'info')
    {
        if ((int)Config::get('swoole.http.options.daemonize') <= 0) {
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