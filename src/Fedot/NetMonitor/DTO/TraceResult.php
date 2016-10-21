<?php

namespace Fedot\NetMonitor\DTO;

class TraceResult
{
    /**
     * @var int
     */
    public $step;

    /**
     * @var string
     */
    public $ip1;

    /**
     * @var string
     */
    public $ip2;

    /**
     * @var string
     */
    public $ip3;

    /**
     * @var string
     */
    public $latency1;

    /**
     * @var string
     */
    public $latency2;

    /**
     * @var string
     */
    public $latency3;
}
