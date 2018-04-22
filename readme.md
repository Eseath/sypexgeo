# SypexGeo PHP API

[![Latest stable version](https://poser.pugx.org/eseath/sypexgeo/v/stable)](https://packagist.org/packages/eseath/sypexgeo)
[![Build Status](https://travis-ci.org/Eseath/sypexgeo.svg?branch=master)](https://travis-ci.org/Eseath/sypexgeo)

A PHP package for working with the [SypexGeo](https://sypexgeo.net) database file.

The current version supports Laravel 5.5 and later. If you need support Laravel 5.4 or older, see version 1.*.

## Installation for Laravel >=5.5

1\. Add the package through composer:

```
composer require eseath/sypexgeo
```

2\. Publish config `sxgeo.php` (optionally):

```
php artisan vendor:publish --provider="Eseath\SxGeo\SxGeoServiceProvider"
```

By default in config specified URL to the database of cities. If you want the database of countries, change url:

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

Also you can download the database manually:

* [Сountries](https://sypexgeo.net/files/SxGeoCountry.zip)
* [Сities](https://sypexgeo.net/files/SxGeoCity_utf8.zip)

## Usage

```php
use Eseath\SxGeo\SxGeo;

$sxGeo = new SxGeo('/path/to/database/file.dat');
$fullInfo  = $sxGeo->getCityFull($ip)
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