<?php

namespace Fedot\NetMonitor\Service;


use DI\Annotation\Inject;
use Fedot\NetMonitor\Model\Request;
use Fedot\NetMonitor\Service\Handler\AbstractRequestHandler;

class RequestManager
{
    /**
     * @var AbstractRequestHandler[]
     */
    protected $requestHandlers = [];

    /**
     * @Inject({"request-manager.handlers"})
     * 
     * @param AbstractRequestHandler[] $handlers
     */
    public function setHandlers(array $handlers)
    {
        $this->requestHandlers = $handlers;
    }

    /**
     * @param Request $request
     */
    public function handle(Request $request)
    {
        foreach ($this->requestHandlers as $requestHandler) {
            if ($requestHandler->isSupport($request)) {
                $requestHandler->handle($request);
            }
        }
    }
}
