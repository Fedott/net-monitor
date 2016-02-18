<?php

namespace Fedot\NetMonitor\Service;


use DI\Annotation\Inject;
use JJG\Ping;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;

class PingService
{
    /**
     * @var string[]
     */
    protected $ipForPing = [];

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
     * @var WebSocketServer
     */
    protected $webSocketServer;

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
     * @param WebSocketServer $webSocketServer
     *
     * @return $this
     */
    public function setWebSocketServer(WebSocketServer $webSocketServer)
    {
        $this->webSocketServer = $webSocketServer;

        return $this;
    }

    /**
     * @param string $ip
     */
    public function startPing(string $ip)
    {
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

        $this->webSocketServer->sendToAll($result);
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

            $this->eventLoop->cancelTimer($this->pingTimer);
        }
    }
}
