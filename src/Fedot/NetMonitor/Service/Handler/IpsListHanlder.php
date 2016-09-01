<?php

namespace Fedot\NetMonitor\Service\Handler;


use DI\Annotation\Inject;
use Fedot\NetMonitor\DTO\Connection;
use Fedot\NetMonitor\Model\Request;
use Fedot\NetMonitor\Model\Response;
use Fedot\NetMonitor\Service\ConnectionsAnalyzer;
use Fedot\NetMonitor\Service\RouterCommandService;

class IpsListHanlder extends AbstractRequestHandler
{
    const COMMAND = 'getIps';

    /**
     * @var RouterCommandService
     */
    protected $routerService;

    /**
     * @var ConnectionsAnalyzer
     */
    protected $connectionsAnalyzer;

    /**
     * @var array
     */
    protected $localIpRanges;

    /**
     * IpsListHanlder constructor.
     */
    public function __construct()
    {
        $this->localIpRanges = [
            [ip2long('10.0.0.0'), ip2long('10.255.255.255')],
            [ip2long('172.16.0.0'), ip2long('172.31.255.255')],
            [ip2long('192.168.0.0'), ip2long('192.168.255.255')],
        ];
    }

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
     * @Inject
     *
     * @param ConnectionsAnalyzer $connectionsAnalyzer
     *
     * @return $this
     */
    public function setConnectionsAnalyzer(ConnectionsAnalyzer $connectionsAnalyzer)
    {
        $this->connectionsAnalyzer = $connectionsAnalyzer;

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
     * @param Connection $connection
     *
     * @return string
     */
    protected function getIpFromConnection(Connection $connection)
    {
        foreach ($this->localIpRanges as $localIpRange) {
            if ($localIpRange[0] < ip2long($connection->getDestination())
                && ip2long($connection->getDestination()) < $localIpRange[1]
            ) {
                return $connection->getSource();
            }
        }

        return $connection->getDestination();
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        $connections = $this->routerService->getConnections();
        if (!$request->getParam('withoutFilter')) {
            $connections = $this->connectionsAnalyzer->filter($connections);
        }
        $response = new Response();

        $response->setRequestId($request->getId());

        $result = [
            'ips' => [],
        ];
        foreach ($connections as $connection) {
            $result['ips'][] = $this->getIpFromConnection($connection);
        }

        $response->setResult($result);

        $request->getTargetConnection()->send(json_encode($response));
    }
}
