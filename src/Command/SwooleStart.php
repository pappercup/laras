<?php

namespace Pappercup\Command;

use Pappercup\Core\SwooleBridge;
use Pappercup\Core\SwooleHttp;
use Pappercup\Http\Request;

class SwooleStart extends SwooleBridge
{
    protected $app = null;
    protected $http = null;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'started swoole in laravel';

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
        (app(SwooleHttp::class))->start();
    }

}