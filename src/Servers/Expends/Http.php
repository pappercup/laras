<?php

namespace Pappercup\Servers\Expends;

use Pappercup\Contracts\Server\Expends\ContractHttp;
use Pappercup\Support\ArrayableTrait;
use Swoole\Http\Server;

class Http extends Server implements ContractHttp, \ArrayAccess
{
    use ArrayableTrait;
}