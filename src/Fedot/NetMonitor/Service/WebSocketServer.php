<?php

namespace Fedot\NetMonitor\Service;

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
        $this->connections->attach($conn);
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn)
    {
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
