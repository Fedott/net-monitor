<?php

use function \DI\add;
use function \DI\get;
use function \DI\object;
use Fedot\NetMonitor\Command\MonitorCommand;
use Fedot\NetMonitor\Command\ServerCommand;

return [
    'console.commands' => add([
        get(MonitorCommand::class),
        get(ServerCommand::class),
    ]),
    \Symfony\Component\Console\Application::class => object()
        ->method('addCommands', get('console.commands')),
    \React\EventLoop\LoopInterface::class => \DI\factory([React\EventLoop\Factory::class, 'create']),

];
