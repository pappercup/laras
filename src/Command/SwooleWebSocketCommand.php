<?php

namespace Pappercup\Command;

use Illuminate\Console\Command;
use Pappercup\Support\ServerCommandTrait;

class SwooleWebSocketCommand extends Command
{
    use ServerCommandTrait;

    protected $server_type = 'websocket';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:websocket {action : start|stop|reload|restart}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'bridge swoole and laravel: start|stop|reload|restart';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->init();
        $this->act();
    }


}