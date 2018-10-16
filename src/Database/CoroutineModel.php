<?php

namespace Pappercup\Database;

use Illuminate\Database\Eloquent\Model;
use Pappercup\Pool\PoolMySQL;

class CoroutineModel extends Model
{

    /**
     * Resolve a connection instance.
     *
     * @param  string|null  $connection
     * @return \Illuminate\Database\Connection
     */
    public static function resolveConnection($connection = null)
    {
        return PoolMySQL::get();
//        return static::$resolver->connection($connection);
    }


}