# SypexGeo PHP API

[![Latest stable version](https://poser.pugx.org/eseath/sypexgeo/v/stable)](https://packagist.org/packages/eseath/sypexgeo)
[![Build Status](https://travis-ci.org/Eseath/sypexgeo.svg?branch=master)](https://travis-ci.org/Eseath/sypexgeo)

A PHP package for working with the [SypexGeo](https://sypexgeo.net) database file.

Supports Laravel 5.* ([see wiki](https://github.com/Eseath/sypexgeo/wiki))

## Installation

1\. Add the package through composer:

    composer require eseath/sypexgeo

2\. Download the database file of [countries](https://sypexgeo.net/files/SxGeoCountry.zip) or [cities](https://sypexgeo.net/files/SxGeoCity_utf8.zip).

## Usage

```php
use Eseath\SxGeo\SxGeo;

$sxGeo = new SxGeo('/path/to/database/file.dat');
$fullInfo  = $sxGeo->getCityFull($ip)
$briefInfo = $sxGeo->get($ip);
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