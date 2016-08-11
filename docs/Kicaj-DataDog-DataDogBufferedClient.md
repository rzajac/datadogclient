## Class Kicaj\DataDog\DataDogBufferedClient
DataDog buffered client.
Very simple PHP [DataDog](http://www.datadoghq.com/) client.
## Extends

- Kicaj\DataDog\DataDogClient

## Constants

```php
const UDP_BLOCKING = 'UDP_BLOCKING';
const UDP_NON_BLOCKING = 'UDP_NON_BLOCKING';
const UDP_FILE = 'file';
const EVENT_VIA_UDP = 'UDP';
const EVENT_VIA_HTTP = 'TCP';
const SERVICE_OK = 0;
const SERVICE_WARNING = 1;
const SERVICE_CRITICAL = 2;
const SERVICE_UNKNOWN = 3;
const CFG_SERVER = 'server';
const CFG_SERVER_PORT = 'serverPort';
const CFG_DATADOG_URL = 'datadogUrl';
const CFG_EVENT_PATH = 'eventPath';
const CFG_SSL_VERIFY_HOST = 'curlSslVerifyHost';
const CFG_SSL_VERIFY_PEER = 'curlSslVerifyPeer';
const CFG_UDP_KIND = 'udpKind';
const CFG_EVENTS_VIA = 'eventsVia';
const CFG_OUT_FILE_PATH = 'outFilePath';
```

## Methods

|                                  |                                  |                                  |                                  |                                  |
| -------------------------------- | -------------------------------- | -------------------------------- | -------------------------------- | -------------------------------- |
|   [__construct](#__construct)    | [getBufferSize](#getbuffersize)  |          [send](#send)           |   [flushBuffer](#flushbuffer)    |    [__destruct](#__destruct)     |

## Properties

|                                    |                                    |                                    |                                    |
| ---------------------------------- | ---------------------------------- | ---------------------------------- | ---------------------------------- |
|         [$buffer](#buffer)         |  [$maxBufferSize](#maxbuffersize)  |         [$config](#config)         |         [$apiKey](#apikey)         |

-------

#### $config
DataDog client configuration.

```php
protected array $config = array(self::CFG_SERVER => 'localhost', self::CFG_SERVER_PORT => 8125, self::CFG_DATADOG_URL => 'https://app.datadoghq.com', self::CFG_EVENT_PATH => '/api/v1/events', self::CFG_SSL_VERIFY_HOST => 2, self::CFG_SSL_VERIFY_PEER => true, self::CFG_UDP_KIND => self::UDP_NON_BLOCKING, self::CFG_EVENTS_VIA => self::EVENT_VIA_UDP, self::CFG_OUT_FILE_PATH => '')
```

#### $apiKey
The DataDog API key.

```php
protected string $apiKey = ''
```

-------
## Methods
#### __construct
Constructor.
```php
public function __construct(integer $threshold, string $apiKey) : 
```
Arguments:
- _$threshold_ **integer** - Buffer size., 
- _$apiKey_ **string** - The DataDog API key.

-------
#### getBufferSize
Returns the current size of the buffer.
```php
public function getBufferSize() : integer
```

Returns: **integer**

-------
#### send
Send metric to DataDog - buffered.
```php
public function send(string $metric) : string
```
Arguments:
- _$metric_ **string** - The metric to send to DataDog.

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

Returns: **string**

-------
#### flushBuffer
Send all metrics to DataDog.
```php
public function flushBuffer() : 
```

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### __destruct
Destructor.
```php
public function __destruct() : 
```

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
