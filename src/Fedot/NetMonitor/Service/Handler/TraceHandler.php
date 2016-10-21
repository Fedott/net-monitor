<?php

namespace Fedot\NetMonitor\Service\Handler;

use DI\Annotation\Inject;
use Fedot\NetMonitor\Model\Request;
use Fedot\NetMonitor\Model\Response;
use Fedot\Ping\Service\TraceRoute;
use React\EventLoop\LoopInterface;

class TraceHandler extends AbstractRequestHandler
{
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
     * @param Request $request
     *
     * @return bool
     */
    public function isSupport(Request $request): bool
    {
        if ($request->getCommand() == "startTrace") {
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        $ip = $request->getParams()['ip'];

        $trace = new TraceRoute();
        $trace->setEventLoop($this->eventLoop);
        $trace->setHost($ip);
        $trace->setTraceCallback(function ($output) use ($request) {
            $response = new Response();
            $response->setRequestId($request->getId());
            $response->setResult([
                "content" => $output,
            ]);
            $request->getTargetConnection()->send(json_encode($response));
        });

        $trace->trace();
    }
}
