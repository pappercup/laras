<?php

namespace Pappercup\Exceptions;

class ResourceNotEnoughExceptions extends \Exception {

    protected $code = 50000;
    protected $message = 'warnning: pool resource not enough.';

}

