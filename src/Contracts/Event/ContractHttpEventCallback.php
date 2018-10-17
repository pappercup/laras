<?php
/**
 * Created by PhpStorm.
 * User: pappercup
 * Date: 2018/9/18
 * Time: 13:23
 */

namespace Pappercup\Contracts\Event;


use Illuminate\Foundation\Application;

interface ContractHttpEventCallback extends ContractEventCallback {

    /**
     * @param Application $application
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 15:44
     */
    public static function beforeRunLaravel(Application $application);

    /**
     * @param Application $application
     * @param \Swoole\Http\Response $swooleResponse
     * @param string $content
     * @return mixed
     * @author pappercup
     * @date 2018/9/18 15:44
     */
    public static function afterRunLaravel(Application $application, \Swoole\Http\Response $swooleResponse, string $content);

}

