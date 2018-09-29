<?php

namespace Pappercup\Command;

use Illuminate\Console\Command;
use Pappercup\Support\ServerCommandTrait;

class SwooleHttpCommand extends Command
{
    use ServerCommandTrait;

    protected $server_type = 'http';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:http {action : start|stop|reload|restart}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'bridge swoole and laravel; 
                        swoole:http start|stop|reload|restart; 
                        tips: default is start; 
                        ex: swoole:http start: swoole:http stop: swoole:http;';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->check();
        $this->act();
    }

}