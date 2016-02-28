<?php

namespace Fedot\NetMonitor\Service;

use DI\Annotation\Inject;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class WebSocketServer implements MessageComponentInterface
{
    /**
     * @var \SplObjectStorage|ConnectionInterface[]
     */
    protected $connections;

    /**
     * @var PingService
     */
    protected $pingService;

    /**
     * @var RouterCommandService
     */
    protected $routerService;

    /**
     * @var ConnectionsAnalyzer
     */
    protected $connectionAnalyzer;

    /**
     * @Inject
     *
     * @param ConnectionsAnalyzer $connectionAnalyzer
     *
     * @return $this
     */
    public function setConnectionAnalyzer(ConnectionsAnalyzer $connectionAnalyzer)
    {
        $this->connectionAnalyzer = $connectionAnalyzer;

        return $this;
    }

    /**
     * @Inject
     *
     * @param RouterCommandService $routerService
     *
     * @return $this
     */
    public function setRouterService(RouterCommandService $routerService)
    {
        $this->routerService = $routerService;

        return $this;
    }

    /**
     * @param PingService $pingService
     *
     * @return $this
     */
    public function setPingService(PingService $pingService)
    {
        $this->pingService = $pingService;

        return $this;
    }

    public function __construct()
    {
        $this->connections = new \SplObjectStorage();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        echo "connection open \n";
        $this->connections->attach($conn);
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
        echo "connection close \n";
        $this->connections->detach($conn);
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception          $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {

    }

    /**
     * @param ConnectionInterface $from
     * @param string              $msg
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $message = json_decode($msg, true);
        if (isset($message['command'])) {
            $command = $message['command'];
            if ($command == 'start-ping') {
                $ip = $message['ip'];
                $this->pingService->startPing($ip);
            }
            if ($command == 'stop-ping') {
                $ip = $message['ip'];
                $this->pingService->stopPing($ip);
            }
            if ($command == 'getIps') {
                $connections = $this->routerService->getConnections();
//                $connections = $this->connectionAnalyzer->filter($connections);
                $response = [
                    'id' => $message['id'],
                ];

                $connectionIps = [];
                foreach ($connections as $connection) {
                    $connectionIps[] = $connection->getDestination();
                }

                $response['ips'] = $connectionIps;

                $from->send(json_encode($response));
            }
        }
    }

    /**
     * @param array $message
     */
    public function sendToAll(array $message)
    {
        foreach ($this->connections as $connection) {
            $connection->send(json_encode($message));
        }
    }
}
