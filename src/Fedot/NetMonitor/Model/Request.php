<?php

namespace Fedot\NetMonitor\Model;


use Ratchet\ConnectionInterface;

class Request
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var ConnectionInterface
     */
    protected $targetConnection;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string $command
     *
     * @return $this
     */
    public function setCommand(string $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $param
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getParam($param, $default = null)
    {
        return $this->params[$param] ?? $default;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return ConnectionInterface
     */
    public function getTargetConnection()
    {
        return $this->targetConnection;
    }

    /**
     * @param ConnectionInterface $targetConnection
     *
     * @return $this
     */
    public function setTargetConnection(ConnectionInterface $targetConnection)
    {
        $this->targetConnection = $targetConnection;

        return $this;
    }
}
