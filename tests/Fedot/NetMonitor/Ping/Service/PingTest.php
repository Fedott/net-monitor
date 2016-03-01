<?php

namespace Fedot\NetMonitor\Ping\Service;

use Fedot\Ping\Service\Ping;

class PingTest extends \PHPUnit_Framework_TestCase
{
    public function testParseOutput()
    {
        $output = "PING 8.8.4.4 (8.8.4.4) 56(84) bytes of data.
            64 bytes from 8.8.4.4: icmp_seq=1 ttl=42 time=23.8 ms";

        $ping = new Ping();
        $actualHost = null;
        $actualLatency = null;
        $ping->setPingCallback(function ($host, $latency) use (&$actualHost, &$actualLatency) {
            $actualHost = $host;
            $actualLatency = $latency;
        });

        $ping->parseOutput($output);

        $this->assertEquals('8.8.4.4', $actualHost);
        $this->assertEquals('23.8', $actualLatency);
    }
}
