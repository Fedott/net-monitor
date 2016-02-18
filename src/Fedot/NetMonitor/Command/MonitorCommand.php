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
            ->addOption('whois', 'w', InputOption::VALUE_NONE)
            ->addOption('ping', 'p', InputOption::VALUE_NONE)
            ->addOption('periodic-ping', 'd', InputOption::VALUE_NONE)
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getOption('limit');
        $whois = $input->getOption('whois');
        $ping  = $input->getOption('ping');

        $connections = $this->routerCommandService->getConnections();

        $this->connectionsAnalyzer->setIsNeedPing($ping);
        $this->connectionsAnalyzer->setIsNeedWhois($whois);
        if (null !== $limit) {
            $this->connectionsAnalyzer->setLimitFrequency($limit);
        }

        $connections = $this->connectionsAnalyzer->analyze($connections);

        $table = new Table($output);
        $headers = [
            'destination',
            'frequency',
        ];

        if ($ping) {
            $headers[] = 'latency';
        }

        if ($whois) {
            $headers[] = 'whois';
        }

        $table->setHeaders($headers);

        foreach ($connections as $connection) {
            $row = [
                $connection->getDestination(),
                $connection->getFrequency(),
            ];

            if ($ping) {
                $row[] = $connection->getLatency();
            }

            $table->addRow($row);
        }

        $table->render();

        if ($input->getOption('periodic-ping') && isset($connection)) {
            $i = 1;
            while (1) {

                if ($i % 10 == 0) {
                    $output->write("\x0D");
                }

                $output->write($this->connectionsAnalyzer->updateLatency($connection)->getLatency() . " ");
                $i++;
//                $output->write($i++ . " ");
                usleep(100000);
            }
        }
    }
}
