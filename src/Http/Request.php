<?php

namespace Pappercup\Http;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends \Illuminate\Http\Request
{
    public static $swooleRequest = null;

    public static function captureSwooleRequest(\swoole_http_request $swooleRequest)
    {
        self::$swooleRequest = $swooleRequest;
        parent::enableHttpMethodParameterOverride();

        return parent::createFromBase(self::createFromSwooleGlobals());
    }

    /**
     * Creates a new request with values from PHP's super globals.
     *
     * @return static
     */
    public static function createFromSwooleGlobals()
    {
        // With the php's bug #66606, the php's built-in web server
        // stores the Content-Type and Content-Length header values in
        // HTTP_CONTENT_TYPE and HTTP_CONTENT_LENGTH fields.
        $server = [];
        foreach (self::$swooleRequest->server as $key => $value) {
            $server[strtoupper($key)] = $value;
        }
        if ('cli-server' === PHP_SAPI) {
            if (array_key_exists('HTTP_CONTENT_LENGTH', $_SERVER)) {
                $server['CONTENT_LENGTH'] = $_SERVER['HTTP_CONTENT_LENGTH'];
            }
            if (array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)) {
                $server['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE'];
            }
        }

        $headers = [];
        foreach (self::$swooleRequest->header as $key => $value) {
            $headers['HTTP_' . str_replace('-', '_', strtoupper($key))] = $value;
        }

        $request = static::createRequestFromFactory(
            self::$swooleRequest->get ? self::$swooleRequest->get : [],
            self::$swooleRequest->post ? self::$swooleRequest->post : [],
            array(),
            self::$swooleRequest->cookie ? self::$swooleRequest->cookie : [],
            self::$swooleRequest->files ? self::$swooleRequest->files : [],
            array_merge($server, $headers));

        if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))
        ) {
            parse_str(self::$swooleRequest->rawContent(), $data);
            $request->request = new ParameterBag($data);
        }

        return $request;
    }


    private static function createRequestFromFactory(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        if (self::$requestFactory) {
            $request = \call_user_func(self::$requestFactory, $query, $request, $attributes, $cookies, $files, $server, $content);

            if (!$request instanceof self) {
                throw new \LogicException('The Request factory must return an instance of Symfony\Component\HttpFoundation\Request.');
            }

            return $request;
        }

        return new \Symfony\Component\HttpFoundation\Request($query, $request, $attributes, $cookies, $files, $server, $content);
    }

}