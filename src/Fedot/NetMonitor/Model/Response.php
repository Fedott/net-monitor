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
    protected $body;

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
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param array $body
     *
     * @return $this
     */
    public function setBody(array $body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getRequestId(),
        ] + $this->getBody();
    }
}
