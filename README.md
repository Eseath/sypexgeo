# SypexGeo PHP API

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat)](https://php.net/)
[![Latest stable version](https://poser.pugx.org/eseath/sypexgeo/v/stable)](https://packagist.org/packages/eseath/sypexgeo)
[![Build Status](https://travis-ci.com/Eseath/sypexgeo.svg?branch=master)](https://travis-ci.com/Eseath/sypexgeo)

A PHP package for working with the [SypexGeo](https://sypexgeo.net) database file.

The current version supports Laravel 5.1 and later.

## Installation

```
composer require eseath/sypexgeo
```

For non-Laravel projects, you need to manually download the database file:

* [Countries](https://sypexgeo.net/files/SxGeoCountry.zip)
* [Cities](https://sypexgeo.net/files/SxGeoCity_utf8.zip)

> The database is updated 2 times a month.

## Setup

### Laravel

1\. If Laravel version <= 5.4, add into `config/app.php`:

```php
    'providers' => [
        \Eseath\SxGeo\SxGeoServiceProvider::class,
    ]
```

2\. Publish config `sxgeo.php` (optionally):

```
php artisan vendor:publish --provider="Eseath\SxGeo\SxGeoServiceProvider"
```

By default, in config specified URL to the database of cities. If you want the database of countries, change url:

```
...
    'dbFileURL' => 'https://sypexgeo.net/files/SxGeoCountry.zip',
...
```

3\. Download the database file:

```
php artisan sxgeo:update
```

You can use this command to upgrade database to the current version via CRON.

## Usage

```php
use Eseath\SxGeo\SxGeo;

$sxGeo = new SxGeo('/path/to/database/file.dat');
$fullInfo  = $sxGeo->getCityFull($ip);
$briefInfo = $sxGeo->get($ip);
```

### With Laravel

```php
use SxGeo;

$data = SxGeo::getCityFull($ip);
```

## Example Data

```
array:3 [▼
    "city" => array:5 [▼
        "id" => 524901
        "lat" => 55.75222
        "lon" => 37.61556
        "name_ru" => "Москва"
        "name_en" => "Moscow"
    ]
    "region" => array:4 [▼
        "id" => 524894
        "name_ru" => "Москва"
        "name_en" => "Moskva"
        "iso" => "RU-MOW"
    ]
    "country" => array:6 [▼
        "id" => 185
        "iso" => "RU"
        "lat" => 60
        "lon" => 100
        "name_ru" => "Россия"
        "name_en" => "Russia"
    ]
]
```

```
array:2 [▼
    "city" => array:5 [▼
        "id" => 524901
        "lat" => 55.75222
        "lon" => 37.61556
        "name_ru" => "Москва"
        "name_en" => "Moscow"
    ]
    "country" => array:2 [▼
        "id" => 185
        "iso" => "RU"
    ]
]
```

## Running Tests

The tests are run automatically by Travis CI on multiple PHP and Laravel versions.

If you want to run tests locally, use the following command:

```shell
python3 ./test.py
```

## Development

```shell
docker-compose run php-7.1 composer install
```
