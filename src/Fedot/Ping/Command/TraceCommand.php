<?php

namespace Fedot\Ping\Command;

use DI\Annotation\Inject;
use Fedot\Ping\Service\Ping;
use Fedot\Ping\Service\TraceRoute;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TraceCommand extends Command
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
            ->setName("trace")
            ->addArgument("host")
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

        $trace = new TraceRoute();
        $trace->setHost($host);
        $trace->setEventLoop($this->eventLoop);

        $eventLoop = $this->eventLoop;

        $trace->setTraceCallback(function ($out) use ($output) {
            $output->write($out);
        });

        $trace->setExitCallback(function () use ($eventLoop, $output) {
            $output->writeln("The end");
            $eventLoop->stop();
        });

        $trace->trace();

        $this->eventLoop->run();
    }
}
