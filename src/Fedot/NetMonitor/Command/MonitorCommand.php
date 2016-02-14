<?php

namespace Fedot\NetMonitor\Command;

use DI\Annotation\Inject;
use Fedot\NetMonitor\Service\RouterCommandService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorCommand extends Command
{
    /**
     * @var RouterCommandService
     */
    protected $routerCommandService;

    /**
     * @Inject()
     *
     * @param RouterCommandService $routerCommandService
     *
     * @return $this
     */
    public function setRouterCommandService(RouterCommandService $routerCommandService)
    {
        $this->routerCommandService = $routerCommandService;

        return $this;
    }

    protected function configure()
    {
        $this
            ->setName('monitor')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump(array_filter($this->routerCommandService->parseConnections(), function ($element) {
            return $element['src'] == '192.168.1.47';
        }));
    }
}
