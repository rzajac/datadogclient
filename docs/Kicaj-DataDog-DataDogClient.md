## Class Kicaj\DataDog\DataDogClient
DataDog client.
Very simple PHP [datadogstatsd](http://www.datadoghq.com/) client.
## Constants

```php
const UDP_BLOCKING = 'UDP_BLOCKING';
const UDP_NON_BLOCKING = 'UDP_NON_BLOCKING';
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
```

## Methods

|                                            |                                            |                                            |                                            |
| ------------------------------------------ | ------------------------------------------ | ------------------------------------------ | ------------------------------------------ |
|        [__construct](#__construct)         |          [getApiKey](#getapikey)           |          [setConfig](#setconfig)           |          [getConfig](#getconfig)           |
|          [histogram](#histogram)           |              [gauge](#gauge)               |                [set](#set)                 |            [counter](#counter)             |
|          [increment](#increment)           |          [decrement](#decrement)           |           [eventUdp](#eventudp)            |              [event](#event)               |
|          [eventHttp](#eventhttp)           |         [checkError](#checkerror)          |       [serviceCheck](#servicecheck)        |     [buildEventPost](#buildeventpost)      |
|       [parseAndSend](#parseandsend)        |          [buildTags](#buildtags)           |       [isAssocArray](#isassocarray)        |             [sample](#sample)              |
|               [send](#send)                | [sendUdpNonBlocking](#sendudpnonblocking)  |    [sendUdpBlocking](#sendudpblocking)     |                   [](#)                    |

## Properties

|                      |                      |
| -------------------- | -------------------- |
|  [$config](#config)  |  [$apiKey](#apikey)  |

-------

#### $config
DataDog client configuration.

```php
protected array $config = array(self::CFG_SERVER => 'localhost', self::CFG_SERVER_PORT => 8125, self::CFG_DATADOG_URL => 'https://app.datadoghq.com', self::CFG_EVENT_PATH => '/api/v1/events', self::CFG_SSL_VERIFY_HOST => 2, self::CFG_SSL_VERIFY_PEER => true, self::CFG_UDP_KIND => self::UDP_NON_BLOCKING, self::CFG_EVENTS_VIA => self::EVENT_VIA_UDP)
```

#### $apiKey
The DataDog API key.

```php
protected string $apiKey = ''
```

-------
## Methods
#### __construct
DataDog constructor.
```php
public function __construct(string $apiKey) : 
```
Arguments:
- _$apiKey_ **string** - The DataDog API key.

-------
#### getApiKey
Returns API key.

NOTE: This is here mostly for testing.
```php
public function getApiKey() : string
```

Returns: **string**

-------
#### setConfig
Set DataDog client configuration option.
```php
public function setConfig(string $option, mixed $value) : Kicaj\DataDog\DataDogClient
```
Arguments:
- _$option_ **string** - The one of self::CFG_* constants., 
- _$value_ **mixed** - The value to set option to.

Returns: **[Kicaj\DataDog\DataDogClient](Kicaj-DataDog-DataDogClient.md)**

-------
#### getConfig
Return current DataDog configuration.
```php
public function getConfig() : array
```

Returns: **array**

-------
#### histogram
Send histogram metric.
```php
public function histogram(string $metricName, float $value, array $tags, float $sampleRate) : 
```
Arguments:
- _$metricName_ **string** - The metric name., 
- _$value_ **float** - The metric value., 
- _$tags_ **array** - The associative array of tag =&gt; value., 
- _$sampleRate_ **float** - The rate of sampling 0 to 1 (0-100%).

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### gauge
Send gauge metric.
```php
public function gauge(string $metricName, float $value, array $tags, float $sampleRate) : 
```
Arguments:
- _$metricName_ **string** - The metric name., 
- _$value_ **float** - The metric value., 
- _$tags_ **array** - The associative array of tag =&gt; value., 
- _$sampleRate_ **float** - The rate of sampling 0 to 1 (0-100%).

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### set
Send set metric.
```php
public function set(string $metricName, float $value, array $tags, float $sampleRate) : 
```
Arguments:
- _$metricName_ **string** - The metric name., 
- _$value_ **float** - The metric value., 
- _$tags_ **array** - The associative array of tag =&gt; value., 
- _$sampleRate_ **float** - The rate of sampling 0 to 1 (0-100%).

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### counter
Send counter metric.
```php
public function counter(string $metricName, integer $delta, array $tags, float $sampleRate) : 
```
Arguments:
- _$metricName_ **string** - The metric name., 
- _$delta_ **integer** - The counter delta value., 
- _$tags_ **array** - The associative array of tag =&gt; value., 
- _$sampleRate_ **float** - The rate of sampling 0 to 1 (0-100%).

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### increment
Increment counter metric.
```php
public function increment(string $metricName, array $tags, float $sampleRate) : 
```
Arguments:
- _$metricName_ **string** - The metric name., 
- _$tags_ **array** - The associative array of tag =&gt; value., 
- _$sampleRate_ **float** - The rate of sampling 0 to 1 (0-100%).

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### decrement
Decrement counter metric.
```php
public function decrement(string $metricName, array $tags, float $sampleRate) : 
```
Arguments:
- _$metricName_ **string** - The metric name., 
- _$tags_ **array** - The associative array of tag =&gt; value., 
- _$sampleRate_ **float** - The rate of sampling 0 to 1 (0-100%).

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### eventUdp
Send event to DataDog via UDP.
```php
public function eventUdp(string $title, string $text, array $opt, array $tags) : 
```
Arguments:
- _$title_ **string** - The event title., 
- _$text_ **string** - The event text. Supports line breaks., 
- _$opt_ **array** - The optional fields., 
- _$tags_ **array** - The associative array of tag =&gt; value.

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### event
Send event to DataDog via HTTP.
```php
public function event(string $title, string $text, array $opt, array $tags) : 
```
Arguments:
- _$title_ **string** - The event title., 
- _$text_ **string** - The event text. Supports line breaks., 
- _$opt_ **array** - The optional fields., 
- _$tags_ **array** - The associative array of tag =&gt; value.

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### eventHttp
Send event to DataDog via HTTP.
```php
public function eventHttp(string $title, string $text, array $opt, array $tags) : 
```
Arguments:
- _$title_ **string** - The event title., 
- _$text_ **string** - The event text. Supports line breaks., 
- _$opt_ **array** - The optional fields., 
- _$tags_ **array** - The associative array of tag =&gt; value.

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### checkError
Check DataDog response and throw on error.
```php
public function checkError(integer $httpRespCode, string $respBody) : 
```
Arguments:
- _$httpRespCode_ **integer** - The HTTP response code., 
- _$respBody_ **string** - The DataDog response body.

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### serviceCheck
Send service check.
```php
public function serviceCheck(string $name, integer $status, array $opt, array $tags) : 
```
Arguments:
- _$name_ **string** - The service check name string., 
- _$status_ **integer** - The one of self::SERVICE_* statuses., 
- _$opt_ **array** - The optional fields., 
- _$tags_ **array** - The associative array of tag =&gt; value.

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### buildEventPost
Build POST array for HTTP event.
```php
public function buildEventPost(string $title, string $text, array $opt, array $tags) : array
```
Arguments:
- _$title_ **string** - The event title., 
- _$text_ **string** - The event text. Supports line breaks., 
- _$opt_ **array** - The optional fields., 
- _$tags_ **array** - The associative array of tag =&gt; value.

Returns: **array**

-------
#### parseAndSend
Parse metric data and send to DataDog.
```php
public function parseAndSend(array $data, array $tags, float $sampleRate) : 
```
Arguments:
- _$data_ **array** - The array with metrics., 
- _$tags_ **array** - The associative array of tag =&gt; value to add to metrics., 
- _$sampleRate_ **float** - The rate of sampling 0 to 1 (0-100%).

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### buildTags
Build tags for metric.
```php
public function buildTags(array $tags) : string
```
Arguments:
- _$tags_ **array** - The associative array of tag =&gt; value to add to metrics.

Returns: **string**

-------
#### isAssocArray
Returns true if array is associative array.

NOTE:

- This checks only the first array key if the passed array is mixed type
  it will make the decision based on the first array key.

- If array is empty method returns false.
```php
public function isAssocArray(array $a) : boolean
```
Arguments:
- _$a_ **array** - The array to check.

Returns: **boolean**

-------
#### sample
Sample metrics array.
```php
public function sample(array $data, float $sampleRate) : array
```
Arguments:
- _$data_ **array** - The array with metrics., 
- _$sampleRate_ **float** - The rate of sampling 0 to 1 (0-100%).

Returns: **array**

-------
#### send
Send metric to DataDog.
```php
public function send(string $metric) : 
```
Arguments:
- _$metric_ **string** - The metric to send to DataDog.

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### sendUdpNonBlocking
Send metric to DataDog via non-blocking UDP.
```php
public function sendUdpNonBlocking(string $metric) : 
```
Arguments:
- _$metric_ **string** - The metric to send to DataDog.

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
#### sendUdpBlocking
Send metric to DataDog via blocking UDP.
```php
public function sendUdpBlocking(string $metric) : 
```
Arguments:
- _$metric_ **string** - The metric to send to DataDog.

Throws:
- [Kicaj\DataDog\DataDogClientException](Kicaj-DataDog-DataDogClientException.md)

-------
