<?php
/**
 * Created by PhpStorm.
 * User: pappercup
 * Date: 2018/9/18
 * Time: 17:54
 */

namespace Pappercup\Bridges;


use Pappercup\Contracts\Bridge\ContractPoolBridge;
use Pappercup\Pools\PoolMySQL;

class BridgePool implements ContractPoolBridge
{

    public static function createPoolMysql($server)
    {
        $pool = [];
        $pool = PoolMySQL::instance();
        for ($i = 0; $i < 5; $i++) {
            $pool->put($pool::generator($server));
        }
        return $pool;
    }

}