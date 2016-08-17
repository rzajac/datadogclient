<?php
/**
 * Copyright 2015 Rafal Zajac <rzajac@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace Kicaj\Test\DataDog;

use Kicaj\DataDog\DataDogClient;
use Kicaj\DataDog\DataDogClientException;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_Error_Warning;

/**
 * DataDogClient_Test.
 *
 * @coversDefaultClass \Kicaj\DataDog\DataDogClient
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class DataDogClient_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Get DataDogClient with method send mocked.
     *
     * @param string $expMetric Expected DataDog metric.
     *
     * @return DataDogClient
     */
    public function getMockedSend($expMetric)
    {
        $dd = $this->getMockBuilder(DataDogClient::class)
                   ->setMethods(['send'])
                   ->getMock();

        $dd->expects($this->once())
           ->method('send')
           ->with($this->equalTo($expMetric));

        return $dd;
    }

    /**
     * @covers ::__construct
     * @covers ::getApiKey
     */
    public function test___construct_noApiKey()
    {
        // When
        $dd = new DataDogClient();

        // Then
        $this->assertSame('', $dd->getApiKey());
    }

    /**
     * @covers ::__construct
     * @covers ::getApiKey
     */
    public function test___construct_apiKey()
    {
        // When
        $dd = new DataDogClient('abc');

        // Then
        $this->assertSame('abc', $dd->getApiKey());
    }

    /**
     * @dataProvider histogramProvider
     *
     * @covers ::histogram
     * @covers ::buildTags
     * @covers ::parseAndSend
     *
     * @param string $metricName
     * @param float  $value
     * @param array  $tags
     * @param string $expMetric
     *
     * @throws DataDogClientException
     */
    public function test_histogram($metricName, $value, $tags, $expMetric)
    {
        // Given
        $dd = $this->getMockedSend($expMetric);

        // Then
        $dd->histogram($metricName, $value, $tags);
    }

    public function histogramProvider()
    {
        return [
            ['aaa', 1.23, [], 'aaa:1.23|h'],
            ['aaa.bbb', 1.23, [], 'aaa.bbb:1.23|h'],
            ['aaa', 1.23, ['tag1' => 'tag1v'], 'aaa:1.23|h|#tag1:tag1v'],
            ['aaa', 1.23, ['tag1' => 'tag1v', 'tag2' => 'tag2v'], 'aaa:1.23|h|#tag1:tag1v,tag2:tag2v'],
            ['aaa', 1.23, ['tag1'], 'aaa:1.23|h|#tag1'],
            ['aaa', 1.23, ['tag1', 'tag2'], 'aaa:1.23|h|#tag1,tag2'],
        ];
    }

    /**
     * @dataProvider gaugeProvider
     *
     * @covers ::gauge
     * @covers ::buildTags
     * @covers ::parseAndSend
     *
     * @param string $metricName
     * @param float  $value
     * @param array  $tags
     * @param string $expMetric
     *
     * @throws DataDogClientException
     */
    public function test_gauge($metricName, $value, $tags, $expMetric)
    {
        // Given
        $dd = $this->getMockedSend($expMetric);

        // Then
        $dd->gauge($metricName, $value, $tags);
    }

    public function gaugeProvider()
    {
        return [
            ['aaa', 1.23, [], 'aaa:1.23|g'],
            ['aaa.bbb', 1.23, [], 'aaa.bbb:1.23|g'],
            ['aaa', 1.23, ['tag1' => 'tag1v'], 'aaa:1.23|g|#tag1:tag1v'],
            ['aaa', 1.23, ['tag1' => 'tag1v', 'tag2' => 'tag2v'], 'aaa:1.23|g|#tag1:tag1v,tag2:tag2v'],
            ['aaa', 1.23, ['tag1', 'tag2'], 'aaa:1.23|g|#tag1,tag2'],
            ['aaa', 1.23, ['tag1'], 'aaa:1.23|g|#tag1'],
        ];
    }

    /**
     * @dataProvider setProvider
     *
     * @covers ::set
     * @covers ::buildTags
     * @covers ::parseAndSend
     *
     * @param string $metricName
     * @param float  $value
     * @param array  $tags
     * @param string $expMetric
     *
     * @throws DataDogClientException
     */
    public function test_set($metricName, $value, $tags, $expMetric)
    {
        // Given
        $dd = $this->getMockedSend($expMetric);

        // Then
        $dd->set($metricName, $value, $tags);
    }

    public function setProvider()
    {
        return [
            ['aaa', 1.23, [], 'aaa:1.23|s'],
            ['aaa.bbb', 1.23, [], 'aaa.bbb:1.23|s'],
            ['aaa', 1.23, ['tag1' => 'tag1v'], 'aaa:1.23|s|#tag1:tag1v'],
            ['aaa', 1.23, ['tag1' => 'tag1v', 'tag2' => 'tag2v'], 'aaa:1.23|s|#tag1:tag1v,tag2:tag2v'],
            ['aaa', 1.23, ['tag1'], 'aaa:1.23|s|#tag1'],
            ['aaa', 1.23, ['tag1', 'tag2'], 'aaa:1.23|s|#tag1,tag2'],
        ];
    }

    /**
     * @dataProvider incrementProvider
     *
     * @covers ::increment
     * @covers ::counter
     * @covers ::buildTags
     * @covers ::parseAndSend
     *
     * @param string $metricName
     * @param array  $tags
     * @param string $expMetric
     *
     * @throws DataDogClientException
     */
    public function test_increment($metricName, $tags, $expMetric)
    {
        // Given
        $dd = $this->getMockedSend($expMetric);

        // Then
        $dd->increment($metricName, $tags);
    }

    public function incrementProvider()
    {
        return [
            ['aaa', [], 'aaa:1|c'],
            ['aaa.bbb', [], 'aaa.bbb:1|c'],
            ['aaa', ['tag1' => 'tag1v'], 'aaa:1|c|#tag1:tag1v'],
            ['aaa', ['tag1' => 'tag1v', 'tag2' => 'tag2v'], 'aaa:1|c|#tag1:tag1v,tag2:tag2v'],
            ['aaa', ['tag1'], 'aaa:1|c|#tag1'],
            ['aaa', ['tag1', 'tag2'], 'aaa:1|c|#tag1,tag2'],
        ];
    }

    /**
     * @dataProvider decrementProvider
     *
     * @covers ::decrement
     * @covers ::counter
     * @covers ::buildTags
     * @covers ::parseAndSend
     *
     * @param string $metricName
     * @param array  $tags
     * @param string $expMetric
     *
     * @throws DataDogClientException
     */
    public function test_decrement($metricName, $tags, $expMetric)
    {
        // Given
        $dd = $this->getMockedSend($expMetric);

        // Then
        $dd->decrement($metricName, $tags);
    }

    public function decrementProvider()
    {
        return [
            ['aaa', [], 'aaa:-1|c'],
            ['aaa.bbb', [], 'aaa.bbb:-1|c'],
            ['aaa', ['tag1' => 'tag1v'], 'aaa:-1|c|#tag1:tag1v'],
            ['aaa', ['tag1' => 'tag1v', 'tag2' => 'tag2v'], 'aaa:-1|c|#tag1:tag1v,tag2:tag2v'],
            ['aaa', ['tag1'], 'aaa:-1|c|#tag1'],
            ['aaa', ['tag1', 'tag2'], 'aaa:-1|c|#tag1,tag2'],
        ];
    }

    /**
     * @covers ::parseAndSend
     */
    public function test_parseAndSend_empty()
    {
        // Given
        $dd = $this->getMockBuilder(DataDogClient::class)
                   ->setMethods(['send', 'buildTags'])
                   ->getMock();

        $dd->expects($this->never())->method('send');
        $dd->expects($this->never())->method('buildTags');

        // When
        /** @var DataDogClient $dd */
        $dd->parseAndSend([]);
    }

    /**
     * @covers ::send
     */
    public function test_send_nonBlockingUdp()
    {
        // Given
        $dd = $this->getMockBuilder(DataDogClient::class)
                   ->setMethods(['sendUdpBlocking', 'sendUdpNonBlocking'])
                   ->getMock();

        $dd->expects($this->once())->method('sendUdpNonBlocking');
        $dd->expects($this->never())->method('sendUdpBlocking');

        /** @var DataDogClient $dd */
        $dd->setConfig(DataDogClient::CFG_UDP_KIND, DataDogClient::UDP_NON_BLOCKING);

        // When
        $dd->increment('aaa');
    }

    /**
     * @covers ::send
     */
    public function test_send_blockingUdp()
    {
        // Given
        $dd = $this->getMockBuilder(DataDogClient::class)
                   ->setMethods(['sendUdpBlocking', 'sendUdpNonBlocking'])
                   ->getMock();

        $dd->expects($this->never())->method('sendUdpNonBlocking');
        $dd->expects($this->once())->method('sendUdpBlocking');

        /** @var DataDogClient $dd */
        $dd->setConfig(DataDogClient::CFG_UDP_KIND, DataDogClient::UDP_BLOCKING);

        // When
        $dd->increment('aaa');
    }

    /**
     * @covers ::send
     *
     * @expectedException \Kicaj\DataDog\DataDogClientException
     * @expectedExceptionMessage Unknown metric send type: __not_supported__
     */
    public function test_send_unsupportedVia()
    {
        // Given
        $dd = new DataDogClient();

        // When
        $dd->setConfig(DataDogClient::CFG_UDP_KIND, '__not_supported__');

        // Then
        $dd->increment('metric');
    }

    /**
     * @covers ::send
     * @covers ::sendToFile
     *
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessageRegExp /Filename cannot be empty/
     */
    public function test_send_toFile_error()
    {
        // Given
        $dd = new DataDogClient();

        // When
        $dd->setConfig(DataDogClient::CFG_UDP_KIND, DataDogClient::UDP_FILE);

        // Then
        $dd->increment('metric');
    }

    /**
     * @covers ::send
     * @covers ::sendToFile
     */
    public function test_send_toFile()
    {
        // Given
        $root = vfsStream::setup('dir');
        $testFile = vfsStream::newFile('metrics.txt')->at($root);
        $dd = new DataDogClient();

        // When
        $dd->setConfig(DataDogClient::CFG_UDP_KIND, DataDogClient::UDP_FILE);
        $dd->setConfig(DataDogClient::CFG_OUT_FILE_PATH, $testFile->url());

        // Then
        $dd->increment('metric');
        $dd->event('title', 'text', [], ['tag1']);

        $expected = "metric:1|c\n_e{5,4}:title|text|#tag1\n";
        $this->assertSame($expected, $testFile->getContent());
    }

    /**
     * @covers ::event
     */
    public function test_event_viaHttp()
    {
        // Given
        $dd = $this->getMockBuilder(DataDogClient::class)
                   ->setMethods(['eventHttp', 'eventUdb'])
                   ->getMock();

        $dd->expects($this->once())->method('eventHttp');
        $dd->expects($this->never())->method('eventUdb');

        /** @var DataDogClient $dd */
        $dd->setConfig(DataDogClient::CFG_EVENTS_VIA, DataDogClient::EVENT_VIA_HTTP);

        // When
        $dd->event('title', 'text');
    }

    /**
     * @covers ::event
     */
    public function test_event_viaUdp()
    {
        // Given
        $dd = $this->getMockBuilder(DataDogClient::class)
                   ->setMethods(['eventHttp', 'eventUdp'])
                   ->getMock();

        $dd->expects($this->once())->method('eventUdp');
        $dd->expects($this->never())->method('eventHttp');

        /** @var DataDogClient $dd */
        $dd->setConfig(DataDogClient::CFG_EVENTS_VIA, DataDogClient::EVENT_VIA_UDP);

        // When
        $dd->event('title', 'text');
    }

    /**
     * @covers ::event
     *
     * @expectedException \Kicaj\DataDog\DataDogClientException
     * @expectedExceptionMessage Unknown event send type: __not_supported__
     */
    public function test_event_unsupportedVia()
    {
        // Given
        $dd = new DataDogClient();

        // When
        $dd->setConfig(DataDogClient::CFG_EVENTS_VIA, '__not_supported__');

        // Then
        $dd->event('title', 'message');
    }

    /**
     * @dataProvider eventProvider
     *
     * @covers ::event
     * @covers ::eventUdp
     *
     * @param string $title
     * @param string $text
     * @param array  $opt
     * @param array  $tags
     * @param string $expMetric
     *
     * @throws DataDogClientException
     */
    public function test_event($title, $text, $opt, $tags, $expMetric)
    {
        // Given
        $dd = $this->getMockedSend($expMetric);

        // When
        $dd->event($title, $text, $opt, $tags);
    }

    public function eventProvider()
    {
        return [
            ['title', 'text', [], [], '_e{5,4}:title|text'],
            ['title', 'text', ['date_happened' => 123], [], '_e{5,4}:title|text|d:123'],
            ['title', 'text', ['hostname' => 'host'], [], '_e{5,4}:title|text|h:host'],
            ['title', 'text', ['aggregation_key' => 'abc'], [], '_e{5,4}:title|text|k:abc'],
            ['title', 'text', ['priority' => 'low'], [], '_e{5,4}:title|text|p:low'],
            ['title', 'text', ['source_type_name' => 'type'], [], '_e{5,4}:title|text|s:type'],
            ['title', 'text', ['alert_type' => 'a_type'], [], '_e{5,4}:title|text|t:a_type'],
            ['title', 'text', ['not_supported' => 'not'], [], '_e{5,4}:title|text'],
            ['title', 'text', ['alert_type' => 'a_type'], ['tag1'], '_e{5,4}:title|text|t:a_type|#tag1'],
            ['title', 'text', ['alert_type' => 'a_type'], ['tag1', 'tag2'], '_e{5,4}:title|text|t:a_type|#tag1,tag2'],
            ['title', 'text', ['alert_type' => 'a_type'], ['tag1' => 'v1'], '_e{5,4}:title|text|t:a_type|#tag1:v1'],
            ['title', 'text', ['alert_type' => 'a_type'], ['tag1' => 'v1', 'tag2' => 'v2'], '_e{5,4}:title|text|t:a_type|#tag1:v1,tag2:v2'],
        ];
    }

    /**
     * @dataProvider configProvider
     *
     * @covers ::setConfig
     * @covers ::getConfig
     *
     * @param string $key
     * @param mixed  $val
     */
    public function test_configProvider($key, $val)
    {
        // Given
        $dd = new DataDogClient();
        $oldCfg = $dd->getConfig();

        // When
        $dd->setConfig($key, $val);
        $newConfig = $dd->getConfig();

        // Then
        $this->assertNotSame($oldCfg[$key], $newConfig[$key]);
        $this->assertSame($val, $newConfig[$key]);

        $changedKeys = array_keys(array_diff($oldCfg, $dd->getConfig()));
        $this->assertSame([$key], $changedKeys);
    }

    public function configProvider()
    {
        if (function_exists('socket_create')) {
            $udpKind = DataDogClient::UDP_BLOCKING;
        } else {
            $udpKind = DataDogClient::UDP_NON_BLOCKING;
        }

        return [
            [DataDogClient::CFG_SERVER, 'abc'],
            [DataDogClient::CFG_SERVER_PORT, 1234],
            [DataDogClient::CFG_DATADOG_URL, 'http://example.com'],
            [DataDogClient::CFG_EVENT_PATH, '/new/path'],
            [DataDogClient::CFG_SSL_VERIFY_HOST, 124],
            [DataDogClient::CFG_SSL_VERIFY_PEER, false],
            [DataDogClient::CFG_UDP_KIND, $udpKind],
            [DataDogClient::CFG_EVENTS_VIA, DataDogClient::EVENT_VIA_HTTP],
        ];
    }

    /**
     * @dataProvider buildEventPostProvider
     *
     * @covers ::buildEventPost
     *
     * @param string $title
     * @param string $text
     * @param array  $opt
     * @param array  $tags
     * @param array  $exp
     */
    public function test_buildEventPost($title, $text, $opt, $tags, $exp)
    {
        // Given
        $dd = new DataDogClient();

        // When
        $post = $dd->buildEventPost($title, $text, $opt, $tags);

        // Then
        $this->assertSame($exp, $post);
    }

    public function buildEventPostProvider()
    {
        return [
            ['title', 'txt', [], [], ['title' => 'title', 'text' => 'txt']],
            // ---
            ['title', 'txt', ['not_valid' => 'host'], [], ['title' => 'title', 'text' => 'txt']],
            // ---
            [
                'title',
                'txt',
                ['date_happened' => 'value1'],
                [],
                ['title' => 'title', 'text' => 'txt', 'date_happened' => 'value1'],
            ],
            // ---
            [
                'title',
                'txt',
                ['aggregation_key' => 'value2'],
                [],
                ['title' => 'title', 'text' => 'txt', 'aggregation_key' => 'value2'],
            ],
            // ---
            [
                'title',
                'txt',
                ['priority' => 'value3'],
                [],
                ['title' => 'title', 'text' => 'txt', 'priority' => 'value3'],
            ],
            // ---
            [
                'title',
                'txt',
                ['source_type_name' => 'value4'],
                [],
                ['title' => 'title', 'text' => 'txt', 'source_type_name' => 'value4'],
            ],
            // ---
            [
                'title',
                'txt',
                ['alert_type' => 'value5', 'priority' => 'value3'],
                [],
                ['title' => 'title', 'text' => 'txt', 'priority' => 'value3', 'alert_type' => 'value5'],
            ],
            // ---
            [
                'title',
                'txt',
                ['alert_type' => 'value5', 'priority' => 'value3'],
                ['tag1'],
                [
                    'title'      => 'title',
                    'text'       => 'txt',
                    'priority'   => 'value3',
                    'alert_type' => 'value5',
                    'tags'       => ['tag1'],
                ],
            ],
            // ---
            [
                'title',
                'txt',
                ['alert_type' => 'value5', 'priority' => 'value3'],
                ['tag1', 'tag2'],
                [
                    'title'      => 'title',
                    'text'       => 'txt',
                    'priority'   => 'value3',
                    'alert_type' => 'value5',
                    'tags'       => ['tag1', 'tag2'],
                ],
            ],
            // ---
            [
                'title',
                'txt',
                ['alert_type' => 'value5', 'priority' => 'value3'],
                ['tag1' => 'val1'],
                [
                    'title'      => 'title',
                    'text'       => 'txt',
                    'priority'   => 'value3',
                    'alert_type' => 'value5',
                    'tags'       => ['tag1:val1'],
                ],
            ],
            // ---
            [
                'title',
                'txt',
                ['alert_type' => 'value5', 'priority' => 'value3'],
                ['tag1' => 'val1', 'tag2' => 'val2'],
                [
                    'title'      => 'title',
                    'text'       => 'txt',
                    'priority'   => 'value3',
                    'alert_type' => 'value5',
                    'tags'       => ['tag1:val1', 'tag2:val2'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider isAssocArrayProvider
     *
     * @covers ::isAssocArray
     *
     * @param bool  $exp
     * @param array $arr
     */
    public function test_isAssocArray($exp, $arr)
    {
        // Given
        $dd = new DataDogClient();

        // When
        $got = $dd->isAssocArray($arr);

        // Then
        $this->assertSame($exp, $got);
    }

    public function isAssocArrayProvider()
    {
        return [
            [true, ['tag' => 'val']],
            [false, ['tag1', 'tag2']],
        ];
    }

    /**
     * @dataProvider serviceCheckProvider
     *
     * @covers ::serviceCheck
     *
     * @param string $name
     * @param int    $status
     * @param array  $opt
     * @param array  $tags
     * @param string $expMetric
     *
     * @throws DataDogClientException
     */
    public function test_serviceCheck($name, $status, $opt, $tags, $expMetric)
    {
        // Given
        $dd = $this->getMockedSend($expMetric);

        // Then
        $dd->serviceCheck($name, $status, $opt, $tags);
    }

    public function serviceCheckProvider()
    {
        return [
            ['name', 1, [], [], '_sc|name|1'],
            ['name', 2, ['not_supported' => 1], [], '_sc|name|2'],
            ['name', 2, ['timestamp' => 123], [], '_sc|name|2|d:123'],
            ['name', 2, ['hostname' => 'host'], [], '_sc|name|2|h:host'],
            ['name', 2, [], ['tag1' => 'val1'], '_sc|name|2|#tag1:val1'],
            ['name', 2, [], ['tag1'], '_sc|name|2|#tag1'],
            ['name', 2, [], ['tag1', 'tag2'], '_sc|name|2|#tag1,tag2'],
            ['name', 2, ['hostname' => 'host'], ['tag1' => 'val1'], '_sc|name|2|h:host|#tag1:val1'],
            // ---
            [
                'name',
                2,
                ['hostname' => 'host'],
                ['tag1' => 'val1', 'tag2' => 'val2'],
                '_sc|name|2|h:host|#tag1:val1,tag2:val2',
            ],
            // ---
            [
                'name',
                2,
                ['hostname' => 'host', 'message' => 'test message'],
                ['tag1' => 'val1', 'tag2' => 'val2'],
                '_sc|name|2|h:host|#tag1:val1,tag2:val2|m:test message',
            ],
            // ---
            [
                'name',
                2,
                ['message' => 'test message', 'hostname' => 'host'],
                ['tag1' => 'val1', 'tag2' => 'val2'],
                '_sc|name|2|h:host|#tag1:val1,tag2:val2|m:test message',
            ],
            // ---
        ];
    }

    /**
     * @dataProvider sampleProvider
     *
     * @covers ::sample
     *
     * @param float $sampleRate
     * @param int   $minAvgRemoved
     * @param int   $maxAvgRemoved
     */
    public function test_sample($sampleRate, $minAvgRemoved, $maxAvgRemoved)
    {
        // Given
        $dd = new DataDogClient();

        // When
        $avgRemoved = 0;
        for ($i = 0; $i < 100; $i++) {
            $got = $dd->sample(self::HUNDRED_METRICS, $sampleRate);
            if ($avgRemoved === 0) {
                $avgRemoved = 100 - count($got);
            } else {
                $avgRemoved = ($avgRemoved + (100 - count($got))) / 2;
            }
        }

        // Then
        $this->assertTrue($avgRemoved >= $minAvgRemoved && $avgRemoved <= $maxAvgRemoved);
    }

    public function sampleProvider()
    {
        return [
            [1, 0, 0],
            [0.5, 45, 55],
        ];
    }

    /**
     * @covers ::checkError
     */
    public function test_checkError_responseOk()
    {
        // Given
        $dd = new DataDogClient();

        // Then
        $dd->checkError(200, '{"status": "ok"}');
        $dd->checkError(202, '{"status": "ok"}');

        $this->assertTrue(true);
    }

    /**
     * @covers ::checkError
     *
     * @expectedException \Kicaj\DataDog\DataDogClientException
     * @expectedExceptionMessageRegExp /failed with code: 400/
     */
    public function test_checkError_badResponse()
    {
        // Given
        $dd = new DataDogClient();

        // Then
        $dd->checkError(400, '{"status": "fail"}');
    }

    /**
     * @covers ::checkError
     *
     * @expectedException \Kicaj\DataDog\DataDogClientException
     * @expectedExceptionMessageRegExp /failed: no body/
     */
    public function test_checkError_noBody()
    {
        // Given
        $dd = new DataDogClient();

        // Then
        $dd->checkError(200, '');
    }

    /**
     * @covers ::checkError
     *
     * @expectedException \Kicaj\DataDog\DataDogClientException
     * @expectedExceptionMessageRegExp /API status not OK, body: {"status": "fail"}/
     */
    public function test_checkError_statusFail()
    {
        // Given
        $dd = new DataDogClient();

        // Then
        $dd->checkError(200, '{"status": "fail"}');
    }

    /**
     * @covers ::checkError
     *
     * @expectedException \Kicaj\DataDog\DataDogClientException
     * @expectedExceptionMessageRegExp /could not decode response body/
     */
    public function test_checkError_couldNotDecodeBody()
    {
        // Given
        $dd = new DataDogClient();

        // Then
        $dd->checkError(200, '{{{{');
    }

    const HUNDRED_METRICS = [
        'metric0'  => 'value0',
        'metric1'  => 'value1',
        'metric2'  => 'value2',
        'metric3'  => 'value3',
        'metric4'  => 'value4',
        'metric5'  => 'value5',
        'metric6'  => 'value6',
        'metric7'  => 'value7',
        'metric8'  => 'value8',
        'metric9'  => 'value9',
        'metric10' => 'value10',
        'metric11' => 'value11',
        'metric12' => 'value12',
        'metric13' => 'value13',
        'metric14' => 'value14',
        'metric15' => 'value15',
        'metric16' => 'value16',
        'metric17' => 'value17',
        'metric18' => 'value18',
        'metric19' => 'value19',
        'metric20' => 'value20',
        'metric21' => 'value21',
        'metric22' => 'value22',
        'metric23' => 'value23',
        'metric24' => 'value24',
        'metric25' => 'value25',
        'metric26' => 'value26',
        'metric27' => 'value27',
        'metric28' => 'value28',
        'metric29' => 'value29',
        'metric30' => 'value30',
        'metric31' => 'value31',
        'metric32' => 'value32',
        'metric33' => 'value33',
        'metric34' => 'value34',
        'metric35' => 'value35',
        'metric36' => 'value36',
        'metric37' => 'value37',
        'metric38' => 'value38',
        'metric39' => 'value39',
        'metric40' => 'value40',
        'metric41' => 'value41',
        'metric42' => 'value42',
        'metric43' => 'value43',
        'metric44' => 'value44',
        'metric45' => 'value45',
        'metric46' => 'value46',
        'metric47' => 'value47',
        'metric48' => 'value48',
        'metric49' => 'value49',
        'metric50' => 'value50',
        'metric51' => 'value51',
        'metric52' => 'value52',
        'metric53' => 'value53',
        'metric54' => 'value54',
        'metric55' => 'value55',
        'metric56' => 'value56',
        'metric57' => 'value57',
        'metric58' => 'value58',
        'metric59' => 'value59',
        'metric60' => 'value60',
        'metric61' => 'value61',
        'metric62' => 'value62',
        'metric63' => 'value63',
        'metric64' => 'value64',
        'metric65' => 'value65',
        'metric66' => 'value66',
        'metric67' => 'value67',
        'metric68' => 'value68',
        'metric69' => 'value69',
        'metric70' => 'value70',
        'metric71' => 'value71',
        'metric72' => 'value72',
        'metric73' => 'value73',
        'metric74' => 'value74',
        'metric75' => 'value75',
        'metric76' => 'value76',
        'metric77' => 'value77',
        'metric78' => 'value78',
        'metric79' => 'value79',
        'metric80' => 'value80',
        'metric81' => 'value81',
        'metric82' => 'value82',
        'metric83' => 'value83',
        'metric84' => 'value84',
        'metric85' => 'value85',
        'metric86' => 'value86',
        'metric87' => 'value87',
        'metric88' => 'value88',
        'metric89' => 'value89',
        'metric90' => 'value90',
        'metric91' => 'value91',
        'metric92' => 'value92',
        'metric93' => 'value93',
        'metric94' => 'value94',
        'metric95' => 'value95',
        'metric96' => 'value96',
        'metric97' => 'value97',
        'metric98' => 'value98',
        'metric99' => 'value99',
    ];
}
