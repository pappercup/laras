<?php

namespace Pappercup\Pools;

use Illuminate\Database\Connectors\MySqlConnector;
use Pappercup\Contracts\Pool\ContractPoolMySQL;
use Pappercup\Database\ConnectionMySQL;

class PoolMySQL extends Pool implements ContractPoolMySQL
{

    public static function generator(): Object
    {
        $config = self::config();

        $connection = (new MySqlConnector())->connect($config);

        return (new ConnectionMySQL($connection, $config['database'], $config['prefix'], $config));
    }

    protected static function config()
    {
        $config = app()['config']['database.connections']['mysql'];

        return $config;
    }

}