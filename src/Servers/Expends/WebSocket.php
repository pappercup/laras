<?php

namespace Pappercup\Servers\Expends;

use Pappercup\Contracts\Server\Expends\ContractWebSocket;
use Pappercup\Support\ArrayableTrait;
use Swoole\WebSocket\Server;

class WebSocket extends Server implements ContractWebSocket, \ArrayAccess
{
    use ArrayableTrait;
}