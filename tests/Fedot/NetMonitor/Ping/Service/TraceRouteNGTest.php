<?php

namespace Fedot\NetMonitor\Ping\Service;

use Fedot\NetMonitor\DTO\TraceResult;
use Fedot\Ping\Service\TraceRouteNG;
use PHPUnit_Framework_TestCase;

class TraceRouteNGTest extends PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $traceOutput = <<<TEXT
 1?: [LOCALHOST]                                         pmtu 1500
 1:  192.168.1.1                                           0.614ms 
 1:  192.168.1.1                                           0.350ms 
 2:  10.0.196.1                                            1.579ms 
 3:  172.18.2.173                                         15.377ms asymm  9 
 4:  172.18.2.33                                           5.549ms asymm  8 
 5:  172.18.2.18                                           3.302ms asymm  7 
 6:  172.18.2.1                                            1.622ms asymm  5 
 7:  192.168.254.249                                       1.502ms asymm  6 
 8:  188.134.127.17                                        1.710ms asymm  7 
 9:  188.134.126.93                                        2.793ms asymm  8 
10:  87.245.228.194                                        2.274ms asymm 11 
11:  87.245.233.74                                        13.587ms asymm 12 
12:  194.68.123.187                                       14.102ms asymm 13 
13:  184.105.64.105                                       42.130ms 
14:  72.52.92.13                                          45.861ms asymm 11 
15:  184.105.81.77                                       113.741ms asymm 12 
16:  184.105.81.213                                      167.753ms 
17:  72.52.92.117                                        175.102ms asymm 16 
18:  64.71.184.46                                        170.323ms asymm 17 
19:  49.255.255.24                                       335.992ms asymm 23 
20:  114.31.199.37                                       337.606ms asymm 23 
     Too many hops: pmtu 1500
     Resume: pmtu 1500
TEXT;


    }

    public function testOnData()
    {
        $traceData = [];
        $traceData[] = "traceroute to 8.8.8.8 (8.8.8.8), 20 hops max, 60 byte packets";
        $traceData[] = "
 1  192.168.1.1  0.325 ms  0.382 ms  0.406 ms";
        $traceData[] = "
 2  10.0.196.1  1.717 ms";
        $traceData[] = "  1.898 ms";
        $traceData[] = "  2.791 ms";
        $traceData[] = "
 3  172.18.2.173  8.405 ms  9.158 ms  9.858 ms";
        $traceData[] = "
 4  172.18.2.33  14.041 ms";
        $traceData[] = "  15.036 ms";
        $traceData[] = "  16.098 ms
 5  172.18.2.18  2.718 ms  2.737 ms  3.401 ms
 6  172.18.2.1  2.050 ms  2.028 ms  2.201 ms
 7  192.168.254.249  1.933 ms  1.107 ms  1.145 ms
 8  188.134.127.17  1.300 ms  1.315 ms  1.485 ms";
        $traceData[] = "
 9  188.134.126.133  19.848 ms";
        $traceData[] = "  19.825 ms  19.822 ms
10  188.234.141.218  1.643 ms 188.234.140.226  1.529 ms  1.717 ms
11  194.226.100.138  1.979 ms  2.049 ms  2.277 ms
12  216.239.42.95  2.099 ms  2.791 ms  2.686 ms";
        $traceData[] = "
13  216.239.42.85  15.759 ms 216.239.42.53  15.173 ms 216.239.42.85  15.511 ms
14  209.85.249.173  15.529 ms 216.239.40.237  15.476 ms 108.170.235.240  15.292 ms";
        $traceData[] = "
15  216.239.40.174  20.489 ms";
        $traceData[] = " 209.85.247.79  21.258 ms 216.239.40.174  20.331 ms";
        $traceData[] = "
16  *";
        $traceData[] = " * *";
        $traceData[] = "
17  *";
        $traceData[] = " * *
18  *";
        $traceData[] = " * *
19  *";
        $traceData[] = " * *
20  *";
        $traceData[] = " *";
        $traceData[] = " *
";
        $traceData[] = "";

        $expectedData = [];
        $expectedData[] = 'traceroute to 8.8.8.8 (8.8.8.8), 20 hops max, 60 byte packets';
        $expectedData[] = '1  192.168.1.1  0.325 ms  0.382 ms  0.406 ms';
        $expectedData[] = '2  10.0.196.1  1.717 ms  1.898 ms  2.791 ms';
        $expectedData[] = '3  172.18.2.173  8.405 ms  9.158 ms  9.858 ms';
        $expectedData[] = '4  172.18.2.33  14.041 ms  15.036 ms  16.098 ms';
        $expectedData[] = '5  172.18.2.18  2.718 ms  2.737 ms  3.401 ms';
        $expectedData[] = '6  172.18.2.1  2.050 ms  2.028 ms  2.201 ms';
        $expectedData[] = '7  192.168.254.249  1.933 ms  1.107 ms  1.145 ms';
        $expectedData[] = '8  188.134.127.17  1.300 ms  1.315 ms  1.485 ms';
        $expectedData[] = '9  188.134.126.133  19.848 ms  19.825 ms  19.822 ms';
        $expectedData[] = '10  188.234.141.218  1.643 ms 188.234.140.226  1.529 ms  1.717 ms';
        $expectedData[] = '11  194.226.100.138  1.979 ms  2.049 ms  2.277 ms';
        $expectedData[] = '12  216.239.42.95  2.099 ms  2.791 ms  2.686 ms';
        $expectedData[] = '13  216.239.42.85  15.759 ms 216.239.42.53  15.173 ms 216.239.42.85  15.511 ms';
        $expectedData[] = '14  209.85.249.173  15.529 ms 216.239.40.237  15.476 ms 108.170.235.240  15.292 ms';
        $expectedData[] = '15  216.239.40.174  20.489 ms 209.85.247.79  21.258 ms 216.239.40.174  20.331 ms';
        $expectedData[] = '16  * * *';
        $expectedData[] = '17  * * *';
        $expectedData[] = '18  * * *';
        $expectedData[] = '19  * * *';
        $expectedData[] = '20  * * *';
        $expectedLineCount = 21;
        $actualLineCount = 0;

        /** @var TraceRouteNG|\PHPUnit_Framework_MockObject_MockObject $traceRouteNG */
        $traceRouteNG = $this->createPartialMock(TraceRouteNG::class, [
            'parseDataLine'
        ]);

        $traceRouteNG->method('parseDataLine')->with($this->callback(function (string $dataLine) use (&$expectedData, &$actualLineCount) {
            $this->assertEquals($expectedData[$actualLineCount], $dataLine);
            $actualLineCount++;

            return true;
        }));

        foreach ($traceData as $line) {
            $traceRouteNG->onData($line);
        }
        $this->assertEquals($expectedLineCount, $actualLineCount);
    }

    /**
     * @dataProvider providerParseDataLine
     *
     * @param string $line
     * @param TraceResult $expectedResult
     */
    public function testParseDataLine(string $line, TraceResult $expectedResult)
    {
        $traceRouteNG = new TraceRouteNG();

        $actualResult = $traceRouteNG->parseDataLine($line);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function providerParseDataLine()
    {
        $result1 = new TraceResult();
        $result1->step = '18';
        $result1->ip2 = '178.236.0.130';
        $result1->latency2 = '51.650';

        $result2 = new TraceResult();
        $result2->step = '16';
        $result2->ip1 = '176.32.106.36';
        $result2->latency1 = '49.845';
        $result2->ip3 = '176.32.106.34';
        $result2->latency3 = '51.382';

        $result3 = new TraceResult();
        $result3->step = '17';
        $result3->ip1 = '178.236.0.101';
        $result3->latency1 = '51.731';
        $result3->ip2 = '176.32.106.51';
        $result3->latency2 = '51.525';
        $result3->ip3 = '178.236.0.225';
        $result3->latency3 = '50.003';

        $result4 = new TraceResult();
        $result4->step = '12';
        $result4->ip1 = '87.245.246.31';
        $result4->latency1 = '35.251';
        $result4->latency2 = '35.054';
        $result4->latency3 = '35.111';

        $result5 = new TraceResult();
        $result5->step = '19';
        $result5->ip1 = '178.236.1.19';
        $result5->latency1 = '51.586';

        $result6 = new TraceResult();
        $result6->step = '13';

        return [
            1 => ['18  * 178.236.0.130  51.650 ms *', $result1],
            2 => ['16  176.32.106.36  49.845 ms * 176.32.106.34  51.382 ms', $result2],
            3 => ['17  178.236.0.101  51.731 ms 176.32.106.51  51.525 ms 178.236.0.225  50.003 ms', $result3],
            4 => ['12  87.245.246.31  35.251 ms  35.054 ms  35.111 ms', $result4],
            5 => ['19  178.236.1.19  51.586 ms * *', $result5],
            5 => ['13  * * *', $result6],
        ];
    }
}
