<?php

namespace Fedot\NetMonitor\Command;


use DI\Annotation\Inject;
use Fedot\NetMonitor\Service\HttpServer;
use Fedot\NetMonitor\Service\ServerApplication;
use Fedot\NetMonitor\Service\WebSocketServer;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerCommand extends Command
{
    /**
     * @var WebSocketServer
     */
    protected $messenger;

    /**
     * @var HttpServer
     */
    protected $httpServer;

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

    /**
     * @Inject
     *
     * @param HttpServer $httpServer
     *
     * @return $this
     */
    public function setHttpServer(HttpServer $httpServer)
    {
        $this->httpServer = $httpServer;

        return $this;
    }

    /**
     * @Inject
     *
     * @param WebSocketServer $messenger
     *
     * @return $this
     */
    public function setMessenger(WebSocketServer $messenger)
    {
        $this->messenger = $messenger;

        return $this;
    }

    protected function configure()
    {
        $this
            ->setName('server:start')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Start server");

        $app = new ServerApplication($this->eventLoop);

        $app->route('/', $this->httpServer, ['*'], '');
        $app->route('/ping', $this->messenger, ['*'], '');
        $app->route('/{path}/{file}', $this->httpServer, ['*'], '');
        $app->route('/{file}', $this->httpServer, ['*'], '');

        $this->eventLoop->run();

        $output->writeln("Server shutdown");
    }
}
