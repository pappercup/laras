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
        $config = (config('swoole.pool'));
        $pool = new PoolMySQL($server, $config);
        $length = isset($config['mysql']['size']) ? $config['mysql']['size'] : 5;

        for ($i = 0; $i < $length; $i++) {
            $pool->put($pool->generator());
        }
        return $pool;
    }

}