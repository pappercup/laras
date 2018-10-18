<?php

namespace Pappercup\Database;

use Illuminate\Database\Connectors\MySqlConnector;
use Illuminate\Database\MySqlConnection;

class ConnectionMySQL extends MySqlConnection
{

    protected function run($query, $bindings, \Closure $callback)
    {
        dump('============before run query===========');
        $result = parent::run($query, $bindings, $callback);
        // 回收 数据库连接
        dump('---------' . app()['pool.mysql']->count() . '-------------');
        app()['pool.mysql']->put($this);
        dump('---------' . app()['pool.mysql']->count() . '-------------');

        return $result;

    }

    public function reconnect()
    {
        try {

            $config = $this->getConfig();

            $connection = (new MySqlConnector())->connect($config);

            $this->setPdo($connection);
            $this->setReadPdo($connection);

        }catch (\Exception $exception) {
            throw new \LogicException('Lost connection and no reconnector available.');
        }
    }

}