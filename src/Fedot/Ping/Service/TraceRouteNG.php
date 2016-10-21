<?php
namespace Fedot\Ping\Service;

use Fedot\NetMonitor\DTO\TraceResult;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Stream\Stream;

class TraceRouteNG
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
     * @var string
     */
    protected $buffer = '';

    /**
     * @var int
     */
    protected $lastBufferPosition = 0;

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
        $this->process->stdout->on('data', [$this, 'onData']);
    }

    public function stop()
    {
        $this->process->terminate();
    }

    /**
     * @param string $output
     */
    public function onData(string $output)
    {
        $this->buffer .= $output;
        while ($endLinePosition = strpos($this->buffer, "\n", $this->lastBufferPosition)) {
            $dataLine = trim(substr($this->buffer, $this->lastBufferPosition, $endLinePosition - $this->lastBufferPosition));
            $this->lastBufferPosition = $endLinePosition + 1;

            $traceResult = $this->parseDataLine($dataLine);
            if (null !== $traceResult) {
                call_user_func($this->traceCallback, $traceResult);
            }
        }
    }

    public function parseDataLine(string $dataLine)
    {
        $traceRegexp = '/^\s*(?<step>[0-9]+)\s+(\*|((?<ip1>[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})\s+(?<latency1>[0-9\.]+)\s+ms))\s+(\*|((?<ip2>[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})?\s*(?<latency2>[0-9\.]+)\s+ms))\s+(\*|((?<ip3>[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})?\s*(?<latency3>[0-9\.]+)\s+ms))/';
        if (preg_match($traceRegexp, $dataLine, $matches)) {
            $traceResult = new TraceResult();
            $traceResult->step = $matches['step'];
            $traceResult->ip1 = $matches['ip1'] ?? null;
            $traceResult->latency1 = $matches['latency1'] ?? null;
            $traceResult->ip2 = $matches['ip2'] ?? null;
            $traceResult->latency2 = $matches['latency2'] ?? null;
            $traceResult->ip3 = $matches['ip3'] ?? null;
            $traceResult->latency3 = $matches['latency3'] ?? null;

            return $traceResult;
        }

        return null;
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
        $command = "traceroute {$this->host} -m 20 -n";

        return $command;
    }
}
