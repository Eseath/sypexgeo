<?php

declare(strict_types=1);

namespace Eseath\SxGeo\Tests;

use Eseath\SxGeo\SxGeo;

class CountryDatabaseTest extends TestCase
{
    /** @var SxGeo */
    protected $db;

    public function setUp() : void
    {
        parent::setUp();

        config()->set('sxgeo.localPath', __DIR__ . '/.assets/SxGeoCountry.dat');

        $this->db = $this->app->make(SxGeo::class);
    }

    public function dataset() : array
    {
        return [
            ['109.72.73.80', 'RU'],
            ['67.207.76.98', 'DE'],
            ['64.233.165.100', 'US'],
        ];
    }

    /** @dataProvider dataset */
    public function testResultOfCityDatabaseQuerying(string $ip, string $country_code) : void
    {
        $this->assertSame($country_code, $this->db->getCountry($ip));
    }
}
