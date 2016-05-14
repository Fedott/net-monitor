<?php
namespace Fedot\Ping\Service;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Stream\Stream;

class TracePath
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
     * @var callable
     */
    protected $traceCallback;

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
     * @return callable
     */
    public function getTraceCallback()
    {
        return $this->traceCallback;
    }

    /**
     * @param callable $traceCallback
     *
     * @return $this
     */
    public function setTraceCallback(callable $traceCallback)
    {
        $this->traceCallback = $traceCallback;

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
    
    public function trace()
    {
        $command = $this->getCommand();

        $this->process = new Process($command, null, ['LC_ALL' => 'C']);

        if (null !== $this->exitCallback) {
            $this->process->on('exit', [$this, 'exitProcessCallback']);
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
        call_user_func($this->traceCallback, $output);
    }

    public function exitProcessCallback()
    {
        call_user_func($this->exitCallback, $this);
    }

    /**
     * @return string
     */
    protected function getCommand()
    {
        $command = "traceroute {$this->host} -m 20";

        return $command;
    }
}
