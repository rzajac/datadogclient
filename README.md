# DataDog Client

Very simple [DataDog](http://www.datadoghq.com/) client.

## Installation via Composer

```json
{
    "require": {
        "rzajac/datadogclient": "0.6.*"
    }
}
```

## Documentation

[Documentation](docs/index.md)

## Run unit tests

```
$ composer install
$ vendor/bin/phpunit --coverage-html=coverage

................................................................. 65 / 98 ( 66%)
.................................                                 98 / 98 (100%)

Time: 974 ms, Memory: 8.00MB

OK (98 tests, 120 assertions)

Code Coverage Report:
  2016-08-11 21:51:18

 Summary:
  Classes: 66.67% (2/3)
  Methods: 89.66% (26/29)
  Lines:   77.63% (118/152)

\Kicaj\DataDog::DataDogBufferedClient
  Methods: 100.00% ( 5/ 5)   Lines: 100.00% ( 13/ 13)
\Kicaj\DataDog::DataDogClient
  Methods:  87.50% (21/24)   Lines:  75.54% (105/139)
```
