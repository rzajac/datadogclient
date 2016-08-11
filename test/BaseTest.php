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

/**
 * Base class all tests extend.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /** Test port. */
    const TEST_PORT = 65432;

    /**
     * Default DataDog lib configuration.
     *
     * NOTE: DO NOT CHANGE IT IN TESTS!
     *
     * @var array
     */
    protected static $defCfg = [
        'server'               => 'localhost',
        'serverPort'           => 8125,
        'datadogHost'          => '',
        'eventUrl'             => '/api/v1/events',
        'apiKey'               => '',
        'applicationKey'       => '',
        'apiCurlSslVerifyHost' => null,
        'apiCurlSslVerifyPeer' => null,
        'submitEventsOver'     => 'UDP',
    ];

    /**
     * UDP or TCP socket.
     *
     * @var resource
     */
    private static $socket;

    /**
     * Reset Datadog lib configuration to default one.
     */
    public static function resetToDefaultConfig()
    {
        Datadogstatsd::configure('', '', '', 'UDP', 'localhost', 8125, null, null);
    }

    /**
     * Set Datadog ib configuration for tests.
     */
    public static function setTestConfig()
    {
        Datadogstatsd::configure('', '', '', 'UDP', '127.0.0.1', self::TEST_PORT, null, null);
    }

    /**
     * Start UDP server.
     *
     * @param int $port The port to start test server on.
     */
    public static function startTestUDPServer($port = self::TEST_PORT)
    {
        //var_dump(stream_get_transports());exit; // TODO: REMOVE THIS!
        //
        //$address = '127.0.0.1';
        //$fullAddress = 'udp://' . $address . ':' . $port;
        //
        //self::$socket = stream_socket_server($fullAddress, $errno, $errstr, STREAM_SERVER_BIND);
        //if (!self::$socket) {
        //    self::fail('Cannot create UDP server.');
        //}

        //$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        //socket_set_nonblock($socket);
        //socket_sendto($socket, '-', 1, 0, $address, $port);
        //socket_close($socket);

        //$handle = fsockopen("udp://127.0.0.1", self::TEST_PORT);
        //stream_set_blocking($handle, FALSE);
        //fwrite($handle, '-');
    }

    /**
     * Get message from test UDP server.
     *
     * @return string
     */
    public static function getUDPMessage()
    {
        $msg = '';
        $address = '127.0.0.1';
        $port = self::TEST_PORT;

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_recvfrom($sock, $msg, 1, 0, $address, $port);


        //$handle = fsockopen("udp://127.0.0.1", self::TEST_PORT);
        //stream_set_blocking($handle, FALSE);
        //
        //socket_recv($handle, $msg, 1024, MSG_WAITALL);
        //
        //while (false !== ($msg = fgetc($handle))) {
        //   var_dump($msg);
        //}

        //$msg = fgets($handle);
        //$msg = fread(self::$socket, 1024);
        //$msg = fread(self::$socket, 1024);
        //$msg = fread(self::$socket, 1024);
        //$msg = stream_socket_recvfrom(self::$socket, 1024, );
        //$msg = stream_socket_recvfrom(self::$socket, 1024);

        //return substr($msg, 1);

        //var_dump($msg);
        //return $msg;
    }

    /**
     * Close test socket.
     */
    public static function closeSocket()
    {
        if (self::$socket) {
            fclose(self::$socket);
            self::$socket = null;
        }
    }
}
