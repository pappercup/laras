<?php

namespace Pappercup\Database;

use Illuminate\Database\MySqlConnection;
use Pappercup\Pool\PoolMySQL;

class ConnectionMySQL extends MySqlConnection
{

    protected function run($query, $bindings, \Closure $callback)
    {
        $result = parent::run($query, $bindings, $callback);
        // 回收 数据库连接
        PoolMySQL::put($this->getPdo());

        return $result;
    }

    public static function registerConnection($connection, $database, $prefix, $config)
    {
        return new static($connection, $database, $prefix, $config);
    }

}