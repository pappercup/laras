<?php

namespace Pappercup\Database;

use Illuminate\Database\MySqlConnection;

class ConnectionMySQL extends MySqlConnection
{

    protected function run($query, $bindings, \Closure $callback)
    {
        $result = parent::run($query, $bindings, $callback);
        // 回收 数据库连接
        dump('---------' . app()['pool.mysql']->count() . '-------------');
        app()['pool.mysql']->put($this);

        return $result;
    }

    public static function registerConnection($connection, $database, $prefix, $config)
    {
        return new static($connection, $database, $prefix, $config);
    }

}