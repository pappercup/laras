<?php

namespace Pappercup\Config;

use Illuminate\Support\Facades\Config;

class Configure
{
    private static $pid_file = null;
    private static $pid_file_name = 'swoole.pid';

    public static function config()
    {
        $res = true;

        $pid_file = rtrim(Config::get('swoole.http.pid_file_path'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . self::$pid_file_name;
        self::$pid_file = $pid_file;
        if (!file_exists($pid_file)) {
            $res = self::createPidFile($pid_file);
        }

        $log_file = Config::get('swoole.http.options.log_file');
        if (!file_exists($log_file)) {
            $res = self::createLogFile($log_file);
        }
        return $res;
    }

    public static function getPid()
    {
        return file_get_contents(self::$pid_file);
    }

    public static function storePid($pid)
    {
        return file_put_contents(self::getPidFile(), $pid);
    }

    public static function getPidFile()
    {
        return self::$pid_file;
    }

    public static function deletePidFile()
    {
        return unlink(self::getPidFile());
    }





    private static function createLogFile($file)
    {
        return self::createFile($file);
    }

    private static function createPidFile($pid_file)
    {
        return self::createFile($pid_file);
    }

    private static function createFile($file)
    {
        return !is_file($file) ? (function ($file) {
            $dir = dirname($file);
            return is_dir($dir) ?: mkdir($dir, 0755, true) ? touch($file) ? true: false: false;
        })($file): true;
    }



}