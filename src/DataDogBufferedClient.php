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
 * DataDog buffered client.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class DataDogBufferedClient extends DataDogClient
{
    /**
     * Buffer of metrics.
     *
     * @var array
     */
    private $buffer = [];

    /**
     * Buffer threshold.
     *
     * @var int
     */
    private $maxBufferSize;

    /**
     * Constructor.
     *
     * @param int    $threshold Buffer size.
     * @param string $apiKey    The DataDog API key.
     */
    public function __construct($threshold = 50, $apiKey = '')
    {
        parent::__construct($apiKey);
        $this->maxBufferSize = $threshold;
    }

    /**
     * Returns the current size of the buffer.
     *
     * @return int
     */
    public function getBufferSize()
    {
        return count($this->buffer);
    }

    /**
     * Send metric to DataDog - buffered.
     *
     * @param string $metric The metric to send to DataDog.
     *
     * @throws DataDogClientException
     *
     * @return string
     */
    public function send($metric)
    {
        $this->buffer[] = $metric;

        if (count($this->buffer) >= $this->maxBufferSize) {
            return $this->flushBuffer();
        }

        return $metric;
    }

    /**
     * Send all metrics to DataDog.
     *
     * @throws DataDogClientException
     */
    public function flushBuffer()
    {
        if ($this->buffer) {
            parent::send(join("\n", $this->buffer));
            $this->buffer = [];
        }
    }

    /**
     * Destructor.
     *
     * @throws DataDogClientException
     */
    public function __destruct()
    {
        $this->flushBuffer();
    }
}
