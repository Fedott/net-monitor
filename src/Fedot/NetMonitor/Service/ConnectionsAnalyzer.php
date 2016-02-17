<?php

namespace Fedot\NetMonitor\Service;


use DI\Annotation\Inject;
use Fedot\NetMonitor\DTO\Connection;
use Predis\Client;

class ConnectionsAnalyzer
{
    /**
     * @var Client
     */
    protected $redisClient;

    /**
     * @var string
     */
    protected $consoleIp;

    /**
     * @Inject
     *
     * @param Client $redisClient
     *
     * @return $this
     */
    public function setRedisClient(Client $redisClient)
    {
        $this->redisClient = $redisClient;

        return $this;
    }

    /**
     * @Inject({"console.ip"})
     *
     * @param string $consoleIp
     *
     * @return $this
     */
    public function setConsoleIp(string $consoleIp)
    {
        $this->consoleIp = $consoleIp;

        return $this;
    }

    /**
     * @param Connection[] $connections
     *
     * @return Connection[]
     */
    public function analyze(array $connections) : array
    {
        $connections = $this->filter($this->consoleIp, $connections);

        $connections = $this->updateFrequency($connections);

        return $connections;
    }

    /**
     * @param string $needed
     * @param Connection[]  $connections
     *
     * @return Connection[]
     */
    public function filter(string $needed, array $connections): array
    {
        return array_filter($connections, function (Connection $element) use ($needed) {
            return $element->getSource() == $needed;
        });
    }

    /**
     * @param Connection[] $connections
     *
     * @return array
     */
    protected function updateFrequency(array $connections) : array
    {
        foreach ($connections as $connection) {
            $connection->setFrequency(
                $this->incrementFrequencyForIp($connection->getDestination())
            );
        }

        return $connections;
    }

    /**
     * @param string $ip
     *
     * @return int
     */
    protected function incrementFrequencyForIp(string $ip) : int
    {
        $redisKeyPrefix = 'connections:analyzer:frequency:-ip';

        $key = "{$redisKeyPrefix}:$ip";

        return $this->redisClient->incr($key);
    }
}
