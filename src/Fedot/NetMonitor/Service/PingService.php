<?php

namespace Fedot\NetMonitor\Service;

use DI\Annotation\Inject;
use Fedot\NetMonitor\Model\Response;
use Fedot\Ping\Service\Ping;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;
use SplObjectStorage;

class PingService
{

    /**
     * @var SplObjectStorage|ConnectionInterface[]
     */
    protected $connections;

    /**
     * @var bool
     */
    protected $isPingStarted = false;

    /**
     * @var Ping[]
     */
    protected $pingProcesses;

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

        if (!isset($this->pingProcesses[$ip])) {
            $pingProcess = new Ping();
            $pingProcess->setHost($ip);
            $pingProcess->setEventLoop($this->eventLoop);
            $pingProcess->setInterval(0.5);
            $pingProcess->setPingCallback([$this, 'pingCallback']);
            $pingProcess->setExitCallback([$this, 'stopCallback']);
            $pingProcess->ping();

            $this->pingProcesses[$ip] = $pingProcess;
        }
    }

    /**
     * @param string $ip
     */
    public function stopPing(string $ip)
    {
        if (isset($this->pingProcesses[$ip])) {
            $this->pingProcesses[$ip]->stop();
        }
    }

    /**
     * @param string $ip
     * @param float  $latency
     */
    public function pingCallback(string $ip, float $latency)
    {
        $this->sendToAll([
            ['ip' => $ip, 'latency' => $latency],
        ]);
    }

    /**
     * @param Ping $pingProcess
     */
    public function stopCallback(Ping $pingProcess)
    {
        if (isset($this->pingProcesses[$pingProcess->getHost()])) {
            unset($this->pingProcesses[$pingProcess->getHost()]);
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
