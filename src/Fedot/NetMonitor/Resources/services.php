<?php

use function \DI\add;
use function \DI\get;
use function \DI\object;
use Fedot\NetMonitor\Command\MonitorCommand;
use Fedot\NetMonitor\Command\ServerCommand;
use Fedot\NetMonitor\Service\Handler\IpsListHanlder;
use Fedot\NetMonitor\Service\Handler\PingHandler;
use Fedot\NetMonitor\Service\Handler\TraceHandler;

return [
    'console.commands' => add([
        get(MonitorCommand::class),
        get(ServerCommand::class),
    ]),
    \Symfony\Component\Console\Application::class => object()
        ->method('addCommands', get('console.commands')),
    \React\EventLoop\LoopInterface::class => \DI\factory([React\EventLoop\Factory::class, 'create']),
    'request-manager.handlers' => add([
        get(IpsListHanlder::class),
        get(PingHandler::class),
        get(TraceHandler::class),
    ]),
];
