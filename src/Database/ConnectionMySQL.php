<?php

namespace Pappercup\Database;

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

}