<?php

namespace Fedot\Ping\Command;

use DI\Annotation\Inject;
use Fedot\Ping\Service\Ping;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PingCommand extends Command
{
    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * @Inject
     * 
     * @param LoopInterface $eventLoop
     *
     * @return $this
     */
    public function setEventLoop(LoopInterface $eventLoop)
    {
        $this->eventLoop = $eventLoop;

        return $this;
    }

    protected function configure()
    {
        $this
            ->setName("ping")
            ->addArgument("host")
            ->addArgument("count", InputArgument::OPTIONAL, '', 10)
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getArgument('host');
        $count = $input->getArgument('count');

        $ping = new Ping();
        $ping->setHost($host);
        $ping->setEventLoop($this->eventLoop);
        $ping->setCount($count);

        $eventLoop = $this->eventLoop;

        $ping->setPingCallback(function ($host, $latency) use ($output) {
            $output->writeln("{$host} - {$latency}");
        });

        $ping->setExitCallback(function () use ($eventLoop, $output) {
            $output->writeln("The end");
            $eventLoop->stop();
        });

        $ping->ping();
        
        $this->eventLoop->run();
    }
}
