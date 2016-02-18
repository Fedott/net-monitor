<?php

namespace Fedot\NetMonitor\Service;


use DI\Annotation\Inject;
use Fedot\NetMonitor\DTO\Connection;
use JJG\Ping;
use PhpWhois\Whois;
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
     * @var bool
     */
    protected $isNeedPing = false;

    /**
     * @var bool
     */
    protected $isNeedWhois = false;

    /**
     * @var int|null
     */
    protected $limitFrequency;

    /**
     * @var int[]
     */
    protected $skippedRange;

    public function __construct()
    {
        $this->skippedRange = [
            [ip2long('192.168.0.0'), ip2long('192.168.255.255')],
            [ip2long('52.0.0.0'), ip2long('52.63.255.255')],
            [ip2long('54.144.0.0'), ip2long('54.159.255.255')],
            [ip2long('162.13.234.64'), ip2long('162.13.234.127')],
            [ip2long('162.13.234.0'), ip2long('162.13.234.31')],
            [ip2long('50.112.0.0'), ip2long('50.112.255.255')],
            [ip2long('159.153.0.0'), ip2long('159.153.255.255')],
            [ip2long('54.22.0.0'), ip2long('54.239.255.255')],
            [ip2long('23.32.0.0'), ip2long('23.67.255.255')],
        ];
    }

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
     * @param bool $isNeedPing
     *
     * @return $this
     */
    public function setIsNeedPing(bool $isNeedPing)
    {
        $this->isNeedPing = $isNeedPing;

        return $this;
    }

    /**
     * @param bool $isNeedWhois
     *
     * @return $this
     */
    public function setIsNeedWhois(bool $isNeedWhois)
    {
        $this->isNeedWhois = $isNeedWhois;

        return $this;
    }

    /**
     * @param string $ip
     *
     * @return bool
     */
    protected function needSkipIp(string $ip)
    {
        $ipLong = ip2long($ip);
        foreach ($this->skippedRange as $range) {
            if ($ipLong >= $range[0] && $ipLong <= $range[1]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Connection[] $connections
     *
     * @return Connection[]
     */
    public function analyze(array $connections) : array
    {
        $connections = $this->filter($connections);

        $connections = $this->updateFrequency($connections);

        if (null !== $this->limitFrequency) {
            $connections = $this->filterByFrequencyLimit($connections);
        }

        $connections = $this->updateInfo($connections);

        return $connections;
    }

    /**
     * @param Connection[]  $connections
     *
     * @return Connection[]
     */
    public function filter(array $connections): array
    {
        return array_filter($connections, [$this, 'filterFunction']);
    }

    /**
     * @param Connection $connection
     *
     * @return bool
     */
    public function filterFunction(Connection $connection)
    {
        if ($connection->getSource() !== $this->consoleIp) {
            return false;
        }

        if ($this->needSkipIp($connection->getDestination())) {
            return false;
        }

        return true;
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

    /**
     * @param int $limitFrequency
     *
     * @return $this
     */
    public function setLimitFrequency(int $limitFrequency)
    {
        $this->limitFrequency = $limitFrequency;

        return $this;
    }

    /**
     * @param Connection[] $connections
     *
     * @return Connection[]
     */
    protected function updateInfo(array $connections) : array
    {
        if (!$this->isNeedPing
            && !$this->isNeedWhois
        ) {
            return $connections;
        }

        foreach ($connections as $connection) {
            if ($this->isNeedPing) {
                $ping = new Ping($connection->getDestination(), 150);
                $latency = $ping->ping();
                $connection->setLatency((int) $latency);
            }

            if ($this->isNeedWhois) {
                $whois = new Whois($connection->getDestination());
                var_dump($whois->lookup());
            }
        }

        return $connections;
    }

    /**
     * @param Connection[] $connections
     *
     * @return Connection[]
     */
    protected function filterByFrequencyLimit(array $connections) : array
    {
        $limit = $this->limitFrequency;

        return array_filter($connections, function (Connection $element) use ($limit) {
            return $element->getFrequency() < $limit;
        });
    }
}
