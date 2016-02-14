<?php

namespace Fedot\NetMonitor\Service;

class RouterCommandServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testParseResponse()
    {
        $responseBody = file_get_contents(__DIR__ . '/Resources/response.xml');

        $expectedResult = [
            [
                'protocol' => 'tcp',
                'src' => '10.0.199.70',
                'dst' => '91.218.112.167',
                'sport' => '59436',
                'dport' => '443',
                'src-out' => '91.218.112.167',
                'dst-out' => '10.0.199.70',
                'sport-out' => '443',
                'dport-out' => '59436',
            ],
            [
                'protocol' => 'tcp',
                'src' => '192.168.1.17',
                'dst' => '213.180.193.119',
                'sport' => '35546',
                'dport' => '443',
                'src-out' => '213.180.193.119',
                'dst-out' => '10.0.199.70',
                'sport-out' => '443',
                'dport-out' => '35546',
            ],
            [
                'protocol' => 'tcp',
                'src' => '192.168.1.35',
                'dst' => '31.13.72.5',
                'sport' => '43701',
                'dport' => '443',
                'src-out' => '31.13.72.5',
                'dst-out' => '10.0.199.70',
                'sport-out' => '443',
                'dport-out' => '43701',
            ],
        ];


        $service = new RouterCommandService();

        $actualResult = $service->parseResponse($responseBody);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
