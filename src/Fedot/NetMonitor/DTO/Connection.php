<?php

namespace Fedot\NetMonitor\DTO;


class Connection
{
    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $destination;

    /**
     * @var int
     */
    protected $sourcePort;

    /**
     * @var int
     */
    protected $destinationPort;

    /**
     * @var string
     */
    protected $sourceOut;

    /**
     * @var string
     */
    protected $destinationOut;

    /**
     * @var int
     */
    protected $sourceOutPort;

    /**
     * @var int
     */
    protected $destinationOutPort;

    /**
     * @var int
     */
    protected $frequency;

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param string $protocol
     *
     * @return $this
     */
    public function setProtocol(string $protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return $this
     */
    public function setSource(string $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     *
     * @return $this
     */
    public function setDestination(string $destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @return int
     */
    public function getSourcePort()
    {
        return $this->sourcePort;
    }

    /**
     * @param int $sourcePort
     *
     * @return $this
     */
    public function setSourcePort(int $sourcePort)
    {
        $this->sourcePort = $sourcePort;

        return $this;
    }

    /**
     * @return int
     */
    public function getDestinationPort()
    {
        return $this->destinationPort;
    }

    /**
     * @param int $destinationPort
     *
     * @return $this
     */
    public function setDestinationPort(int $destinationPort)
    {
        $this->destinationPort = $destinationPort;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceOut()
    {
        return $this->sourceOut;
    }

    /**
     * @param string $sourceOut
     *
     * @return $this
     */
    public function setSourceOut(string $sourceOut)
    {
        $this->sourceOut = $sourceOut;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationOut()
    {
        return $this->destinationOut;
    }

    /**
     * @param string $destinationOut
     *
     * @return $this
     */
    public function setDestinationOut(string $destinationOut)
    {
        $this->destinationOut = $destinationOut;

        return $this;
    }

    /**
     * @return int
     */
    public function getSourceOutPort()
    {
        return $this->sourceOutPort;
    }

    /**
     * @param int $sourceOutPort
     *
     * @return $this
     */
    public function setSourceOutPort(int $sourceOutPort)
    {
        $this->sourceOutPort = $sourceOutPort;

        return $this;
    }

    /**
     * @return int
     */
    public function getDestinationOutPort()
    {
        return $this->destinationOutPort;
    }

    /**
     * @param int $destinationOutPort
     *
     * @return $this
     */
    public function setDestinationOutPort(int $destinationOutPort)
    {
        $this->destinationOutPort = $destinationOutPort;

        return $this;
    }

    /**
     * @return int
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param int $frequency
     *
     * @return $this
     */
    public function setFrequency(int $frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }
}
