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

namespace Kicaj\DataDog;

/**
 * DataDog client.
 *
 * Very simple PHP [datadogstatsd](http://www.datadoghq.com/) client.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class DataDogClient
{
    /** Use blocking UDP to send metrics. */
    const UDP_BLOCKING = 'UDP_BLOCKING';

    /** Use non-blocking UDP to send metrics. */
    const UDP_NON_BLOCKING = 'UDP_NON_BLOCKING';

    /** Send event through UDP. */
    const EVENT_VIA_UDP = 'UDP';

    /** Send event through HTTP (TCP). */
    const EVENT_VIA_HTTP = 'TCP';

    /** Service check statuses. */
    const SERVICE_OK = 0;
    const SERVICE_WARNING = 1;
    const SERVICE_CRITICAL = 2;
    const SERVICE_UNKNOWN = 3;

    /** Datadog client configuration options. */
    const CFG_SERVER = 'server';
    const CFG_SERVER_PORT = 'serverPort';
    const CFG_DATADOG_URL = 'datadogUrl';
    const CFG_EVENT_PATH = 'eventPath';
    const CFG_SSL_VERIFY_HOST = 'curlSslVerifyHost';
    const CFG_SSL_VERIFY_PEER = 'curlSslVerifyPeer';
    const CFG_UDP_KIND = 'udpKind';
    const CFG_EVENTS_VIA = 'eventsVia';

    /**
     * DataDog client configuration.
     *
     * @var array
     */
    protected $config = [
        self::CFG_SERVER          => 'localhost',
        self::CFG_SERVER_PORT     => 8125,
        self::CFG_DATADOG_URL     => 'https://app.datadoghq.com',
        self::CFG_EVENT_PATH      => '/api/v1/events',
        self::CFG_SSL_VERIFY_HOST => 2,
        self::CFG_SSL_VERIFY_PEER => true,
        self::CFG_UDP_KIND        => self::UDP_NON_BLOCKING,
        self::CFG_EVENTS_VIA      => self::EVENT_VIA_UDP,
    ];

    /**
     * The DataDog API key.
     *
     * @var string
     */
    protected $apiKey = '';

    /**
     * DataDog constructor.
     *
     * @param string $apiKey The DataDog API key.
     */
    public function __construct($apiKey = '')
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Returns API key.
     *
     * NOTE: This is here mostly for testing.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set DataDog client configuration option.
     *
     * @param string $option The one of self::CFG_* constants.
     * @param mixed  $value  The value to set option to.
     *
     * @return DataDogClient
     */
    public function setConfig($option, $value)
    {
        $this->config[$option] = $value;

        return $this;
    }

    /**
     * Return current DataDog configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Send histogram metric.
     *
     * @param string $metricName The metric name.
     * @param float  $value      The metric value.
     * @param array  $tags       The associative array of tag => value.
     * @param float  $sampleRate The rate of sampling 0 to 1 (0-100%).
     *
     * @throws DataDogClientException
     */
    public function histogram($metricName, $value, array $tags = [], $sampleRate = 1.0)
    {
        $this->parseAndSend([$metricName => "$value|h"], $tags, $sampleRate);
    }

    /**
     * Send gauge metric.
     *
     * @param string $metricName The metric name.
     * @param float  $value      The metric value.
     * @param array  $tags       The associative array of tag => value.
     * @param float  $sampleRate The rate of sampling 0 to 1 (0-100%).
     *
     * @throws DataDogClientException
     */
    public function gauge($metricName, $value, array $tags = [], $sampleRate = 1.0)
    {
        $this->parseAndSend([$metricName => "$value|g"], $tags, $sampleRate);
    }

    /**
     * Send set metric.
     *
     * @param string $metricName The metric name.
     * @param float  $value      The metric value.
     * @param array  $tags       The associative array of tag => value.
     * @param float  $sampleRate The rate of sampling 0 to 1 (0-100%).
     *
     * @throws DataDogClientException
     */
    public function set($metricName, $value, array $tags = [], $sampleRate = 1.0)
    {
        $this->parseAndSend([$metricName => "$value|s"], $tags, $sampleRate);
    }

    /**
     * Send counter metric.
     *
     * @param string $metricName The metric name.
     * @param int    $delta      The counter delta value.
     * @param array  $tags       The associative array of tag => value.
     * @param float  $sampleRate The rate of sampling 0 to 1 (0-100%).
     *
     * @throws DataDogClientException
     */
    public function counter($metricName, $delta, array $tags = [], $sampleRate = 1.0)
    {
        $this->parseAndSend([$metricName => "$delta|c"], $tags, $sampleRate);
    }

    /**
     * Increment counter metric.
     *
     * @param string $metricName The metric name.
     * @param array  $tags       The associative array of tag => value.
     * @param float  $sampleRate The rate of sampling 0 to 1 (0-100%).
     *
     * @throws DataDogClientException
     */
    public function increment($metricName, array $tags = [], $sampleRate = 1.0)
    {
        $this->counter($metricName, 1, $tags, $sampleRate);
    }

    /**
     * Decrement counter metric.
     *
     * @param string $metricName The metric name.
     * @param array  $tags       The associative array of tag => value.
     * @param float  $sampleRate The rate of sampling 0 to 1 (0-100%).
     *
     * @throws DataDogClientException
     */
    public function decrement($metricName, array $tags = [], $sampleRate = 1.0)
    {
        $this->counter($metricName, -1, $tags, $sampleRate);
    }

    /**
     * Send event to DataDog via UDP.
     *
     * @see http://docs.datadoghq.com/guides/dogstatsd/#events-1
     *
     * @param string $title The event title.
     * @param string $text  The event text. Supports line breaks.
     * @param array  $opt   The optional fields.
     * @param array  $tags  The associative array of tag => value.
     *
     * @throws DataDogClientException
     */
    public function eventUdp($title, $text, array $opt = [], array $tags = [])
    {
        $titleLength = strlen($title);
        $textLength = strlen($text);

        $event = isset($opt['date_happened']) ? '|d:' . $opt['date_happened'] : '';
        $event .= isset($opt['hostname']) ? '|h:' . $opt['hostname'] : '';
        $event .= isset($opt['aggregation_key']) ? '|k:' . $opt['aggregation_key'] : '';
        $event .= isset($opt['priority']) ? '|p:' . $opt['priority'] : '';
        $event .= isset($opt['source_type_name']) ? '|s:' . $opt['source_type_name'] : '';
        $event .= isset($opt['alert_type']) ? '|t:' . $opt['alert_type'] : '';
        $event .= $this->buildTags($tags);

        $this->send('_e{' . $titleLength . ',' . $textLength . '}:' . $event);
    }

    /**
     * Send event to DataDog via HTTP.
     *
     * @see http://docs.datadoghq.com/guides/dogstatsd/#events-1
     *
     * @param string $title The event title.
     * @param string $text  The event text. Supports line breaks.
     * @param array  $opt   The optional fields.
     * @param array  $tags  The associative array of tag => value.
     *
     * @throws DataDogClientException
     */
    public function event($title, $text, array $opt = [], array $tags = [])
    {
        if ($this->config[self::CFG_EVENTS_VIA] == self::EVENT_VIA_HTTP) {
            $this->eventHttp($title, $text, $opt, $tags);
        } else {
            $this->eventUdp($title, $text, $opt, $tags);
        }
    }

    /**
     * Send event to DataDog via HTTP.
     *
     * @see http://docs.datadoghq.com/guides/dogstatsd/#events-1
     *
     * @param string $title The event title.
     * @param string $text  The event text. Supports line breaks.
     * @param array  $opt   The optional fields.
     * @param array  $tags  The associative array of tag => value.
     *
     * @throws DataDogClientException
     */
    public function eventHttp($title, $text, array $opt = [], array $tags = [])
    {
        // Required fields.
        $post = $this->buildEventPost($title, $text, $opt, $tags);

        $url = $this->config[self::CFG_DATADOG_URL] . $this->config[self::CFG_EVENT_PATH] . '?api_key=' . $this->apiKey;
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->config[self::CFG_SSL_VERIFY_PEER]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $this->config[self::CFG_SSL_VERIFY_HOST]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));

        $respBody = curl_exec($curl);
        $httpRespCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($curlErrorNum = curl_errno($curl)) {
            throw new DataDogClientException('Event send (http) failed: ' . curl_error($curl));
        }

        curl_close($curl);

        $this->checkError($httpRespCode, $respBody);
    }

    /**
     * Check DataDog response and throw on error.
     *
     * @param int    $httpRespCode The HTTP response code.
     * @param string $respBody     The DataDog response body.
     *
     * @throws DataDogClientException
     */
    public function checkError($httpRespCode, $respBody)
    {
        if ($httpRespCode !== 200 && $httpRespCode !== 202) {
            throw new DataDogClientException('Event send (http) failed with code: ' . $httpRespCode . ', body: ' . $respBody);
        }

        if (!$respBody) {
            throw new DataDogClientException('Event send (http) failed: no body');
        }

        if (!$decodedJson = json_decode($respBody, true)) {
            throw new DataDogClientException('Event send (http) failed: could not decode response body.');
        }

        if ($decodedJson['status'] !== 'ok') {
            throw new DataDogClientException('Event send (http) failed: API status not OK, body: ' . $respBody);
        }
    }

    /**
     * Send service check.
     *
     * @param string $name   The service check name string.
     * @param int    $status The one of self::SERVICE_* statuses.
     * @param array  $opt    The optional fields.
     * @param array  $tags   The associative array of tag => value.
     *
     * @throws DataDogClientException
     */
    public function serviceCheck($name, $status, array $opt = [], array $tags = [])
    {
        $sc = "_sc|$name|$status";
        $sc .= isset($opt['timestamp']) ? '|d:' . $opt['timestamp'] : '';
        $sc .= isset($opt['hostname']) ? '|h:' . $opt['hostname'] : '';
        $sc .= $this->buildTags($tags);
        $sc .= isset($opt['message']) ? '|m:' . $opt['message'] : '';

        $this->send($sc);
    }

    /**
     * Build POST array for HTTP event.
     *
     * @see http://docs.datadoghq.com/guides/dogstatsd/#events-1
     *
     * @param string $title The event title.
     * @param string $text  The event text. Supports line breaks.
     * @param array  $opt   The optional fields.
     * @param array  $tags  The associative array of tag => value.
     *
     * @return array
     */
    public function buildEventPost($title, $text, array $opt = [], array $tags = [])
    {
        // Required fields.
        $post = [
            'title' => $title,
            'text'  => $text,
        ];

        // Add optional fields.
        $optKeys = ['date_happened', 'hostname', 'aggregation_key', 'priority', 'source_type_name', 'alert_type'];
        foreach ($optKeys as $key) {
            if (isset($opt[$key])) {
                $post[$key] = $opt[$key];
            }
        }

        // Format and add tags.
        $tagsFormatted = [];
        $isArrAssoc = $this->isAssocArray($tags);
        foreach ($tags as $tagKey => $tagValue) {
            $tagsFormatted[] = $isArrAssoc ? "$tagKey:$tagValue" : $tagValue;
        }

        if (!empty($tagsFormatted)) {
            $post['tags'] = $tagsFormatted;
        }

        return $post;
    }

    /**
     * Parse metric data and send to DataDog.
     *
     * @param array $data       The array with metrics.
     * @param array $tags       The associative array of tag => value to add to metrics.
     * @param float $sampleRate The rate of sampling 0 to 1 (0-100%).
     *
     * @throws DataDogClientException
     */
    public function parseAndSend(array $data, array $tags = [], $sampleRate = 1.0)
    {
        $data = $this->sample($data, $sampleRate);

        if (empty($data)) {
            return;
        }

        $tagStr = $this->buildTags($tags);
        foreach ($data as $metric => $value) {
            $this->send("$metric:$value" . $tagStr);
        }
    }

    /**
     * Build tags for metric.
     *
     * @param array $tags The associative array of tag => value to add to metrics.
     *
     * @return string
     */
    public function buildTags(array $tags)
    {
        if (empty($tags)) {
            return '';
        }

        if ($this->isAssocArray($tags)) {
            $str = '';
            foreach ($tags as $tagKey => $tagVal) {
                $str .= "$tagKey:$tagVal,";
            }
            $str = substr($str, 0, -1);
        } else {
            $str = implode(',', $tags);
        }

        return mb_strlen($str) > 0 ? '|#' . $str : '';
    }

    /**
     * Returns true if array is associative array.
     *
     * NOTE:
     *
     * - This checks only the first array key if the passed array is mixed type
     *   it will make the decision based on the first array key.
     *
     * - If array is empty method returns false.
     *
     * @param array $a The array to check.
     *
     * @return bool
     */
    public function isAssocArray(array $a)
    {
        return empty($a) ? false : !is_integer(array_keys($a)[0]);
    }

    /**
     * Sample metrics array.
     *
     * @param array $data       The array with metrics.
     * @param float $sampleRate The rate of sampling 0 to 1 (0-100%).
     *
     * @return array
     */
    public function sample(array $data, $sampleRate)
    {
        if ($sampleRate == 1) {
            return $data;
        }

        $sampledData = [];
        foreach ($data as $stat => $value) {
            if ((mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax()) <= $sampleRate) {
                $sampledData[$stat] = "$value|@$sampleRate";
            }
        }

        return $sampledData;
    }

    /**
     * Send metric to DataDog.
     *
     * @param string $metric The metric to send to DataDog.
     *
     * @throws DataDogClientException
     */
    public function send($metric)
    {
        if ($this->config[self::CFG_UDP_KIND] == self::UDP_NON_BLOCKING) {
            $this->sendUdpNonBlocking($metric);
        } else {
            $this->sendUdpBlocking($metric);
        }
    }

    /**
     * Send metric to DataDog via non-blocking UDP.
     *
     * @param string $metric The metric to send to DataDog.
     *
     * @throws DataDogClientException
     */
    public function sendUdpNonBlocking($metric)
    {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if ($socket === false) {
            throw new DataDogClientException('Cannot create socket: ' . socket_strerror(socket_last_error()));
        }

        socket_set_nonblock($socket);

        if (socket_sendto($socket, $metric, strlen($metric), 0, $this->config[self::CFG_SERVER],
                $this->config[self::CFG_SERVER_PORT]) === false
        ) {
            throw new DataDogClientException('Error sending metric: ' . socket_strerror(socket_last_error()));
        }

        socket_close($socket);
    }

    /**
     * Send metric to DataDog via blocking UDP.
     *
     * @param string $metric The metric to send to DataDog.
     *
     * @throws DataDogClientException
     */
    public function sendUdpBlocking($metric)
    {
        $address = 'udp://' . $this->config[self::CFG_SERVER];
        $fp = fsockopen($address, $this->config[self::CFG_SERVER_PORT], $errNo, $errStr);
        if (!$fp) {
            throw new DataDogClientException('Cannot open ' . $address . '(' . $errStr . ').');
        }

        if (fwrite($fp, $metric) === false) {
            throw new DataDogClientException('Error sending metric.');
        }
        fclose($fp);
    }
}
