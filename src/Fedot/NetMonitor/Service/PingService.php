<?php

namespace Fedot\NetMonitor\Service;

use DI\Annotation\Inject;
use Fedot\NetMonitor\Model\Response;
use JJG\Ping;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;
use SplObjectStorage;

class PingService
{
    /**
     * @var string[]
     */
    protected $ipForPing = [];

    /**
     * @var SplObjectStorage|ConnectionInterface[]
     */
    protected $connections;

    /**
     * @var bool
     */
    protected $isPingStarted = false;

    /**
     * @var TimerInterface
     */
    protected $pingTimer;

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

    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    /**
     * @param string              $ip
     * @param ConnectionInterface $connection
     */
    public function startPing(string $ip, ConnectionInterface $connection)
    {
        $this->connections->attach($connection);

        if (!isset($this->ipForPing[$ip])) {
            $this->ipForPing[$ip] = $ip;
        }

        if (!$this->isPingStarted) {
            $this->startPingLoop();
        }
    }

    /**
     * @param string $ip
     */
    public function stopPing(string $ip)
    {
        if (isset($this->ipForPing[$ip])) {
            unset($this->ipForPing[$ip]);
        }

        if (count($this->ipForPing) < 1) {
            $this->stopPingLoop();
        }
    }

    public function loopCallback()
    {
        $result = [];

        foreach ($this->ipForPing as $ip) {
            $ping = new Ping($ip, 150);
            $latency = $ping->ping();
            $result[$ip] = $latency;
        }

        $this->sendToAll($result);
    }

    public function startPingLoop()
    {
        if (!$this->isPingStarted) {
            $this->isPingStarted = true;

            $this->pingTimer = $this->eventLoop->addPeriodicTimer(0.1, [$this, 'loopCallback']);
        }
    }

    public function stopPingLoop()
    {
        if ($this->isPingStarted) {
            $this->isPingStarted = false;

            $this->connections->removeAll($this->connections);

            $this->eventLoop->cancelTimer($this->pingTimer);
        }
    }

    /**
     * @param array $result
     */
    protected function sendToAll(array $result)
    {
        $response = new Response();
        $response->setResult($result);

        $responseJson = json_encode($response);

        foreach ($this->connections as $connection) {
            $connection->send($responseJson);
        }
    }
}
