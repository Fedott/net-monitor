<?php

namespace Fedot\NetMonitor\Command;


use DI\Annotation\Inject;
use Fedot\NetMonitor\Service\PingService;
use Fedot\NetMonitor\Service\ServerApplication;
use Fedot\NetMonitor\Service\WebSocketServer;
use Ratchet\App;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\LoopInterface;
use React\Socket\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerCommand extends Command
{
    /**
     * @var PingService
     */
    protected $pingService;

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
     * @param PingService $pingService
     *
     * @return $this
     */
    public function setPingService(PingService $pingService)
    {
        $this->pingService = $pingService;

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

        $messenger = new WebSocketServer();
        $messenger->setPingService($this->pingService);

        $this->pingService->setWebSocketServer($messenger);

        $app = new ServerApplication($this->eventLoop);

        $app->route('/', new \Fedot\NetMonitor\Service\HttpServer());
        $app->route('/ping', $messenger);

        $this->eventLoop->run();

        $output->writeln("Server shutdown");
    }
}
