<?php

namespace Fedot\NetMonitor\Service;

use DI\Annotation\Inject;
use Fedot\NetMonitor\DTO\Connection;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class RouterCommandService
{
    /**
     * @var string
     */
    protected $routerIp;

    /**
     * @var string
     */
    protected $routerLogin;

    /**
     * @var string
     */
    protected $routerPassword;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @Inject({
     *     "router.ip",
     *     "router.login",
     *     "router.password",
     * })
     *
     * @param string $ip
     * @param string $login
     * @param string $password
     *
     * @return $this
     */
    public function setRouterParameters(string $ip, string $login, string $password)
    {
        $this->routerIp = $ip;
        $this->routerLogin = $login;
        $this->routerPassword = $password;

        return $this;
    }

    /**
     * @Inject()
     *
     * @param Client $httpClient
     *
     * @return $this
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * @return array
     */
    public function parseConnections()
    {
        $requestBody = <<<XML
<packet ref="/">
    <request id="1" ref="former.formConnections[load]">
        <command name="show system"></command>
    </request>
    <request id="2" ref="former.formConnections[load]">
        <command name="show ip dhcp bindings">
            <pool>_WEBADMIN</pool>
        </command>
    </request>
    <request id="3" ref="former.formConnections[load]">
        <command name="show interface"></command>
    </request>
    <request id="4" ref="former.formConnections[load]">
        <command name="show ip nat"></command>
    </request>
    <request id="5" ref="former.formConnections[load]">
        <config name="known host"></config>
    </request>
    <request id="6" ref="former.formConnections[load]">
        <command name="show ip arp">
            <alive>alive</alive>
        </command>
    </request>
</packet>
XML;

        $response = $this
            ->httpClient
            ->post("http://{$this->routerIp}/ci", [
                RequestOptions::AUTH => [$this->routerLogin, $this->routerPassword, 'digest'],
                'body' => $requestBody,
                'headers' => [
                    'Content-Type' => 'application/xml',
                ]
            ]);

        $responseBody = $response->getBody()->getContents();

        $parsedResponse = $this->parseResponse($responseBody);

        return $parsedResponse;
    }

    public function parseResponse(string $responseBody)
    {
        $responseXml = new \SimpleXMLElement($responseBody);

        $result = [];

        $natResponse = $responseXml->xpath('//response[@id=4]/nat');

        foreach ($natResponse as $natSection) {
            $result[] = [
                'protocol' => (string) $natSection->protocol,
                'src' => (string) $natSection->src,
                'dst' => (string) $natSection->dst,
                'sport' => (string) $natSection->sport,
                'dport' => (string) $natSection->dport,
                'src-out' => (string) $natSection->{"src-out"},
                'dst-out' => (string) $natSection->{"dst-out"},
                'sport-out' => (string) $natSection->{"sport-out"},
                'dport-out' => (string) $natSection->{"dport-out"},
            ];
        }

        return $result;
    }

    /**
     * @return Connection[]
     */
    public function getConnections()
    {
        $connections = $this->parseConnections();

        $result = [];

        foreach ($connections as $connection) {
            $connectionObject = new Connection();
            $connectionObject
                ->setProtocol($connection['protocol'])
                ->setSource($connection['src'])
                ->setSourcePort($connection['sport'])
                ->setSourceOut($connection['src-out'])
                ->setSourceOutPort($connection['sport-out'])
                ->setDestination($connection['dst'])
                ->setDestinationPort($connection['dport'])
                ->setDestinationOut($connection['src-out'])
                ->setDestinationOutPort($connection['dport-out'])
            ;

            $result[] = $connectionObject;
        }

        return $result;
    }
}
