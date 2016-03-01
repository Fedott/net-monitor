<?php

use function \DI\add;
use function \DI\get;
use function \DI\object;
use Fedot\Ping\Command\PingCommand;

return [
    'console.commands' => \DI\add([
        \DI\get(PingCommand::class),
    ]),
];
