<?php

namespace Pappercup\Command;


use Pappercup\Core\SwooleBridge;

class SwooleStop extends SwooleBridge
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'stop swoole';

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
        $this->stop();
    }


    public function stop()
    {
        $pid = $this->getPid();
        if ((int)$pid > 0) {
            $this->info('stopping...');
            $res = $this->killProcss($pid, SIGTERM);
            $this->info($res);
            if ($res) {
                $res = $this->deletePidFile();
                $this->info($res);
            }
        }else{
            $this->info('swoole server is not running!');
        }
    }
}