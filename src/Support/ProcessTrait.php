<?php

namespace Pappercup\Support;

use Swoole\Process;

trait ProcessTrait {

    protected function killProcss($pid, $signo)
    {
        return Process::kill($pid, $signo);
    }

}