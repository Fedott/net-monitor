<?php

namespace Fedot\NetMonitor\Service\Handler;

use Fedot\NetMonitor\Model\Request;
use Fedot\NetMonitor\Model\Response;

class TraceHandler extends AbstractRequestHandler
{
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

        $trace = new TracePath();
        $trace->setHost($ip);
        $trace->setOutputCallback(function ($output) use ($request) {
            $response = new Response();
            $response->setRequestId($request->getId());
            $response->setResult([
                "content" => $output,
            ]);
            $request->getTargetConnection()->send(json_encode($response));
        });

        $trace->start();
    }
}
