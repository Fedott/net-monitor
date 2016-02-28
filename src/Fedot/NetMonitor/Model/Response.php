<?php

namespace Fedot\NetMonitor\Model;


use JsonSerializable;

class Response implements JsonSerializable
{
    /**
     * @var int
     */
    protected $requestId;

    /**
     * @var array
     */
    protected $result;

    /**
     * @return int
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param int $requestId
     *
     * @return $this
     */
    public function setRequestId(int $requestId)
    {
        $this->requestId = $requestId;

        return $this;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param array $result
     *
     * @return $this
     */
    public function setResult(array $result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getRequestId(),
            'result' => $this->getResult(),
        ];
    }
}
