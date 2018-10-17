<?php

namespace Pappercup\Database;

use Illuminate\Database\Eloquent\Model;

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
        return app()['pool.mysql']->get();
//        return static::$resolver->connection($connection);
    }


}