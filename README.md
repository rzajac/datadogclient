# DataDog Client

Very simple [DataDog](http://www.datadoghq.com/) client.

## Installation via Composer

```json
{
    "require": {
        "rzajac/datadogclient": "0.7.*"
    }
}
```

## Documentation

[Documentation](docs/index.md)

## Run unit tests

```
$ vendor/bin/phpunit --coverage-text
PHPUnit 4.8.27 by Sebastian Bergmann and contributors.

Runtime:	PHP 5.5.36 with Xdebug 2.4.1
Configuration:	/Users/thor/ws/DataDog/phpunit.xml

...............................................................  63 / 108 ( 58%)
.............................................

Time: 2.77 seconds, Memory: 10.75MB

OK (108 tests, 131 assertions)

Generating code coverage report in HTML format ... done


Code Coverage Report:
  2016-12-03 12:52:26

 Summary:
  Classes: 66.67% (2/3)
  Methods: 89.66% (26/29)
  Lines:   79.89% (139/174)

\Kicaj\DataDog::DataDogBufferedClient
  Methods: 100.00% ( 5/ 5)   Lines: 100.00% ( 15/ 15)
\Kicaj\DataDog::DataDogClient
  Methods:  87.50% (21/24)   Lines:  77.99% (124/159)
```
