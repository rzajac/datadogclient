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

use Kicaj\DataDog\DataDogBufferedClient;

/**
 * DataDogBufferedClient.
 *
 * @coversDefaultClass \Kicaj\DataDog\DataDogBufferedClient
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class DataDogBufferedClient_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function test___construct_noApiKey()
    {
        // When
        $dd = new DataDogBufferedClient();

        // Then
        $this->assertSame('', $dd->getApiKey());
    }

    /**
     * @covers ::__construct
     */
    public function test___construct_apiKey()
    {
        // When
        $dd = new DataDogBufferedClient(2, 'abc');

        // Then
        $this->assertSame('abc', $dd->getApiKey());
    }

    /**
     * @covers ::getBufferSize
     */
    public function test_getBufferSize()
    {
        // When
        $dd = new DataDogBufferedClient();
        $dd->decrement('name');
        $dd->decrement('name');
        $dd->decrement('name');

        // Then
        $this->assertSame(3, $dd->getBufferSize());
    }

    /**
     * @covers ::__construct
     * @covers ::send
     */
    public function test___construct_threshold()
    {
        // Given
        $dd = $this->getMockBuilder(DataDogBufferedClient::class)
                   ->setConstructorArgs([3])
                   ->setMethods(['flushBuffer'])
                   ->getMock();

        $dd->expects($this->exactly(1))->method('flushBuffer');

        // Then
        /** @var DataDogBufferedClient $dd */
        $dd->increment('name');
        $dd->increment('name');
        $dd->increment('name');
    }

    /**
     * @covers ::__destruct
     * @covers ::send
     */
    public function test___destruct()
    {
        // Given
        $dd = $this->getMockBuilder(DataDogBufferedClient::class)
                   ->setConstructorArgs([3])
                   ->setMethods(['flushBuffer'])
                   ->getMock();

        $dd->expects($this->exactly(1))->method('flushBuffer');

        // Then
        /** @var DataDogBufferedClient $dd */
        $dd->increment('name');
        $dd->increment('name');
        $dd->__destruct();
    }

    /**
     * @covers ::flushBuffer
     */
    public function test_flushBuffer()
    {
        // Given
        $dd = $this->getMockBuilder(DataDogBufferedClient::class)
                   ->setConstructorArgs([3])
                   ->setMethods(['sendUdpNonBlocking'])
                   ->getMock();

        $expected = "name1:1|c\nname2:1|c\nname3:1|c";

        $dd->expects($this->once())
           ->method('sendUdpNonBlocking')
           ->with($this->equalTo($expected));

        // When
        /** @var DataDogBufferedClient $dd */
        $dd->increment('name1');
        $dd->increment('name2');
        $dd->increment('name3');
    }
}
