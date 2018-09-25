<?php

namespace Pappercup\Support;

use Swoole\Process;

trait ProcessTrait {

    protected function killProcss($pid, $signo)
    {
        return Process::kill($pid, $signo);
    }

    protected function isProcessRunning($pid)
    {
        try{
            return Process::kill($pid, 0);
        }catch (\Exception $exception) {
            return false;
        }
    }

}