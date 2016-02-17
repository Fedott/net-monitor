<?php

namespace Fedot\NetMonitor\Command;

use DI\Annotation\Inject;
use Fedot\NetMonitor\Service\ConnectionsAnalyzer;
use Fedot\NetMonitor\Service\RouterCommandService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorCommand extends Command
{
    /**
     * @var RouterCommandService
     */
    protected $routerCommandService;

    /**
     * @var ConnectionsAnalyzer
     */
    protected $connectionsAnalyzer;

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

    /**
     * @Inject
     *
     * @param ConnectionsAnalyzer $connectionsAnalyzer
     *
     * @return $this
     */
    public function setConnectionsAnalyzer(ConnectionsAnalyzer $connectionsAnalyzer)
    {
        $this->connectionsAnalyzer = $connectionsAnalyzer;

        return $this;
    }

    protected function configure()
    {
        $this
            ->setName('monitor')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL)
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getOption('limit');

        $connections = $this->routerCommandService->getConnections();

        $connections = $this->connectionsAnalyzer->analyze($connections);

        $table = new Table($output);
        $table->setHeaders(['destination', 'frequency']);

        foreach ($connections as $connection) {
            if (null !== $limit && $connection->getFrequency() > $limit) {
                continue;
            }

            $table->addRow([
                $connection->getDestination(),
                $connection->getFrequency(),
            ]);
        }

        $table->render();
    }
}
