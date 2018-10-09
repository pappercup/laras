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
    protected $description = 'bridge swoole and laravel;';

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