<?php

use function \DI\add;
use function \DI\get;
use function \DI\object;
use Fedot\NetMonitor\Command\MonitorCommand;

return [
    'console.commands' => add([
        get(MonitorCommand::class),
    ]),
    \Symfony\Component\Console\Application::class => object()
        ->method('addCommands', get('console.commands')),

];
