<?php
/**
 * Created by PhpStorm.
 * User: pappercup
 * Date: 2018/9/21
 * Time: 17:07
 */

namespace Pappercup\Config;

class Environment
{

    const SWOOLE_VERSION = '4.0.0';
    const PHP_VERSION = '7.2.0';
    const LARAVEL_VERSION = '5.5.0';

    private static $error_message = [];

    /**
     * @return bool
     * @author pappercup
     * @date 2018/9/21 17:41
     */
    public static function checkPHPVersion()
    {
        return version_compare(phpversion(), self::PHP_VERSION, '>=') ? true: false;
    }

    /**
     * @return bool
     * @author pappercup
     * @date 2018/9/21 17:41
     */
    public static function checkSwooleVersion()
    {
        return extension_loaded('swoole') ? version_compare(SWOOLE_VERSION, self::SWOOLE_VERSION, '>=') ? true: false: false;
    }

    /**
     * @return bool
     * @author pappercup
     * @date 2018/9/21 17:41
     */
    public static function checkLaravelVersion()
    {
        return version_compare(app()->version(), self::LARAVEL_VERSION, '>=') ? true: false;
    }

    /**
     * check environment
     *
     * @return bool
     * @author pappercup
     * @date 2018/9/21 17:41
     */
    public static function check()
    {
        if (!self::checkPHPVersion()) {
            self::$error_message['php'] = 'php version must be >= ' . self::PHP_VERSION;
            return false;
        }

        if (!self::checkSwooleVersion()) {
            self::$error_message['swoole'] = 'swoole version must be >= ' . self::SWOOLE_VERSION;
            return false;
        }

        if (!self::checkLaravelVersion()) {
            self::$error_message['laravel'] = 'laravel version must be = ' . self::LARAVEL_VERSION;
            return false;
        }

        return true;
    }

    /**
     * return error message
     *
     * @return array
     * @author pappercup
     * @date 2018/9/21 17:43
     */
    public static function getErrorMessage()
    {
        return self::$error_message;
    }

}