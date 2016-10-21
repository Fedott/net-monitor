<?php

namespace Fedot\NetMonitor\DTO;

class TargetIp
{
    /**
     * @var string
     */
    public $ip;

    /**
     * @var bool
     */
    public $isAnalyseStarted = false;

    /**
     * @var string
     */
    public $lastTracedIp;

    /**
     * @var float
     */
    public $traceLatency;

    /**
     * @var float
     */
    public $pingLatency;

    /**
     * @var int
     */
    public $traceSteps = 0;
}
