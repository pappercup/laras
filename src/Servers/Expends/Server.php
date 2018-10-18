<?php

namespace Pappercup\Servers\Expends;

use Pappercup\Contracts\Server\Expends\ContractServer;
use Pappercup\Support\ArrayableTrait;
use Swoole\Server as BaseServer;

class Server extends BaseServer implements ContractServer, \ArrayAccess
{
    use ArrayableTrait;
}