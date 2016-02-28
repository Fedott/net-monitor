<?php

namespace Fedot\NetMonitor\Service;

use DI\Annotation\Inject;
use Fedot\NetMonitor\Model\Request;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class WebSocketServer implements MessageComponentInterface
{
    /**
     * @var \SplObjectStorage|ConnectionInterface[]
     */
    protected $connections;

    /**
     * @var RequestManager
     */
    protected $requestManager;

    /**
     * @Inject
     *
     * @param RequestManager $requestManager
     *
     * @return $this
     */
    public function setRequestManager(RequestManager $requestManager)
    {
        $this->requestManager = $requestManager;

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
        $request = new Request();
        $request->setId($message['id'])
            ->setCommand($message['command'])
            ->setTargetConnection($from)
        ;

        if (array_key_exists('params', $message)) {
            $request->setParams($message['params']);
        }

        $this->requestManager->handle($request);
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
