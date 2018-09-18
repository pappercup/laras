<?php
/**
 * Created by PhpStorm.
 * User: pappercup
 * Date: 2018/9/18
 * Time: 17:54
 */

namespace Pappercup\Core;


class MemoryBridge
{

    public static function createSwooleMemory($memory_config)
    {
        $memory = [];
        if ($memory_config['atomic'] !== false) {
            $memory['atomic'] = new \Swoole\Atomic(empty($memory_config['atomic']['init_value']) ? 0: $memory_config['atomic']['init_value']);
        }

        if ($memory_config['table'] !== false) {
            $size = empty($memory_config['table']['size']) || $memory_config['table']['size'] < 1024  ? 1024: $memory_config['table']['size'];
            $conflict_proportion = empty($memory_config['table']['conflict_proportion']) ||
            $memory_config['table']['conflict_proportion'] > 1 ||
            $memory_config['table']['conflict_proportion'] < 0
                ? 0.2: $memory_config['table']['conflict_proportion'];

            $memory['table'] = new \Swoole\Table($size, $conflict_proportion);
        }

        return $memory;
    }

}