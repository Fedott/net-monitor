<?php

namespace Fedot\NetMonitor\Service\Handler;

use DI\Annotation\Inject;
use Fedot\NetMonitor\Model\Request;
use Fedot\NetMonitor\Service\PingService;

class PingHandler extends AbstractRequestHandler
{
    /**
     * @var PingService
     */
    protected $pingService;

    /**
     * @Inject
     *
     * @param PingService $pingService
     *
     * @return $this
     */
    public function setPingService(PingService $pingService)
    {
        $this->pingService = $pingService;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isSupport(Request $request): bool
    {
        if (in_array($request->getCommand(), ['startPing', 'stopPing'])) {
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function handle(Request $request)
    {
        if ($request->getCommand() == 'startPing') {
            $this->pingService->startPing($request->getParams()['ip'], $request->getTargetConnection());
        } else {
            $this->pingService->stopPing($request->getParams()['ip']);
        }
    }
}
