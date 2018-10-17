<?php

namespace Pappercup\Pools;

use Illuminate\Database\Connectors\MySqlConnector;
use Pappercup\Contracts\Pool\ContractPoolMySQL;
use Pappercup\Database\ConnectionMySQL;

class PoolMySQL extends Pool implements ContractPoolMySQL
{

    public static function generator($server): Object
    {
        $config = $server->config['database']['connections']['mysql'];

        $connection = (new MySqlConnector())->connect($config);

        return (new ConnectionMySQL($connection, $config['database'], $config['prefix'], $config));
    }


}