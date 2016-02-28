<?php

namespace Fedot\NetMonitor\Service\Handler;


use Fedot\NetMonitor\Model\Request;

abstract class AbstractRequestHandler
{
    /**
     * @param Request $request
     *
     * @return bool
     */
    abstract public function isSupport(Request $request): bool;

    /**
     * @param Request $request
     *
     * @return bool
     */
    abstract public function handle(Request $request);
}
