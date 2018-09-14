<?php

namespace Pappercup\Command;

use Pappercup\Core\CommandBridge;

class SwooleReload extends CommandBridge
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:reload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'reload swoole';

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
        $this->reload();
    }


    public function reload()
    {
        $this->info('reload...');
        $pid = $this->getPid();
        if ((int)$pid > 0) {
            $this->info($this->killProcss($pid, SIGUSR1));
        }else{
            $this->info('swoole server is not running!');
        }
    }
}