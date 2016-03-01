<?php

namespace Fedot\Ping\Service;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;

class Ping
{
    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var float
     */
    protected $interval;

    /**
     * @var callable
     */
    protected $pingCallback;

    /**
     * @var callable
     */
    protected $exitCallback;

    /**
     * @var Process
     */
    protected $process;

    /**
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
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function setCount(int $count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return float
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param float $interval
     *
     * @return $this
     */
    public function setInterval(float $interval)
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * @return callable
     */
    public function getPingCallback()
    {
        return $this->pingCallback;
    }

    /**
     * @param callable $pingCallback
     *
     * @return $this
     */
    public function setPingCallback(callable $pingCallback)
    {
        $this->pingCallback = $pingCallback;

        return $this;
    }

    /**
     * @return callable
     */
    public function getExitCallback()
    {
        return $this->exitCallback;
    }

    /**
     * @param callable $exitCallback
     *
     * @return $this
     */
    public function setExitCallback(callable $exitCallback)
    {
        $this->exitCallback = $exitCallback;

        return $this;
    }
    
    public function ping()
    {
        $command = $this->getCommand();

        $this->process = new Process($command, null, ['LC_ALL' => 'C']);
        
        if (null !== $this->exitCallback) {
            $this->process->on('exit', $this->exitCallback);
        }

        $this->process->start($this->eventLoop);
        $this->process->stdout->on('data', [$this, 'parseOutput']);
    }

    public function stop()
    {
        $this->process->terminate();
    }

    /**
     * @param string $output
     */
    public function parseOutput(string $output)
    {
        if (preg_match('/.*from\s(?<host>.*)\:\sicmp_seq.*time=(?<latency>[\d\.]+)/', $output, $matches)) {
            call_user_func($this->pingCallback, $matches['host'], $matches['latency']);
        }
    }

    /**
     * @return string
     */
    protected function getCommand()
    {
        $command = "ping {$this->host}";

        if (null !== $this->count) {
            $command .= " -c {$this->count}";
        }
        
        if (null !== $this->interval) {
            $command .= " -i {$this->interval}";
        }

        return $command;
    }
}
