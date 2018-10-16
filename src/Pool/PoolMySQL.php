<?php

namespace Pappercup\Pool;

use Pappercup\Database\ConnectionMySQL;
use Pappercup\Database\ConnectorMySQL;

class PoolMySQL extends Pool
{

    public static function generator(): Object
    {
        $config = self::config();

        $connection = (new ConnectorMySQL())->connect($config);

        return (new ConnectionMySQL($connection, $config['database'], $config['prefix'], $config));
    }

    protected static function config()
    {
        $config = app()['config']['database.connections']['mysql'];

        return $config;
    }

}