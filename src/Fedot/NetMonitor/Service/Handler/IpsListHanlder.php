<?php

namespace Fedot\NetMonitor\Service\Handler;


use DI\Annotation\Inject;
use Fedot\NetMonitor\Model\Request;
use Fedot\NetMonitor\Model\Response;
use Fedot\NetMonitor\Service\RouterCommandService;

class IpsListHanlder extends AbstractRequestHandler
{
    const COMMAND = 'getIps';

    /**
     * @var RouterCommandService
     */
    protected $routerService;

    /**
     * @Inject
     * 
     * @param RouterCommandService $routerService
     *
     * @return $this
     */
    public function setRouterService(RouterCommandService $routerService)
    {
        $this->routerService = $routerService;

        return $this;
    }
    
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isSupport(Request $request): bool
    {
        if ($request->getCommand() == static::COMMAND) {
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
        $connections = $this->routerService->getConnections();
        $response = new Response();
        
        $response->setRequestId($request->getId());

        $body = [
            'ips' => [],
        ];
        foreach ($connections as $connection) {
            $body['ips'][] = $connection->getDestination();
        }
        
        $response->setBody($body);

        $request->getTargetConnection()->send(json_encode($response));
    }
}
