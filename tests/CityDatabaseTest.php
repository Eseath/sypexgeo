<?php

declare(strict_types=1);

namespace Eseath\SxGeo\Tests;

use Eseath\SxGeo\SxGeo;

class CityDatabaseTest extends TestCase
{
    /** @var SxGeo */
    protected $db;

    public function setUp() : void
    {
        parent::setUp();

        config()->set('sxgeo.localPath', __DIR__ . '/.assets/SxGeoCity.dat');

        $this->db = $this->app->make(SxGeo::class);
    }

    public function dataset() : array
    {
        return [
            ['109.72.73.80', 'RU', 'Russia', 'Россия', 'RU-MOW', 'Moskva', 'Москва', 'Moscow', 'Москва'],
            ['67.207.76.98', 'DE', 'Germany', 'Германия', 'DE-HE', 'Land Hessen', 'Гессен', 'Frankfurt am Main', 'Франкфурт-на-Майне'],
            ['64.233.165.100', 'US', 'United States', 'США', 'US-AR', 'Arkansas', 'Арканзас', 'Mountain View', 'Маунтин-Вью'],
        ];
    }

    /** @dataProvider dataset */
    public function testResultOfCityDatabaseQuerying(
        string $ip,
        string $country_code,
        string $country_name_en,
        string $country_name_ru,
        string $region_code,
        string $region_name_en,
        string $region_name_ru,
        string $city_name_en,
        string $city_name_ru
    ) : void {
        $data = $this->db->getCityFull($ip);

        $this->assertSame($country_code, $data['country']['iso']);
        $this->assertSame($country_name_en, $data['country']['name_en']);
        $this->assertSame($country_name_ru, $data['country']['name_ru']);
        $this->assertSame($region_code, $data['region']['iso']);
        $this->assertSame($region_name_en, $data['region']['name_en']);
        $this->assertSame($region_name_ru, $data['region']['name_ru']);
        $this->assertSame($city_name_en, $data['city']['name_en']);
        $this->assertSame($city_name_ru, $data['city']['name_ru']);
    }
}
