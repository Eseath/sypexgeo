<?php

declare(strict_types=1);

namespace Eseath\SxGeo\Tests;

use Eseath\Sxgeo\DatabaseType;
use Eseath\SxGeo\SxGeo;
use Illuminate\Support\Facades\Artisan;

class UpdateCommandTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        $filepath = config('sxgeo.localPath');

        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    public function testCityDatabase() : void
    {
        config()->set('sxgeo.dbFileURL', 'https://sypexgeo.net/files/SxGeoCity_utf8.zip');

        Artisan::call('sxgeo:update');

        $db = new SxGeo(config('sxgeo.localPath'));
        $metadata = $db->about();

        $this->assertSame(DatabaseType::CITY_EN, $metadata['Type']);
        $this->assertSame('2.2', $metadata['Version']);
        $this->assertSame('utf-8', $metadata['Charset']);
    }

    public function testCountryDatabase() : void
    {
        config()->set('sxgeo.dbFileURL', 'https://sypexgeo.net/files/SxGeoCountry.zip');

        Artisan::call('sxgeo:update');

        $db = new SxGeo(config('sxgeo.localPath'));
        $metadata = $db->about();

        $this->assertSame(DatabaseType::COUNTRY, $metadata['Type']);
        $this->assertSame('2.2', $metadata['Version']);
    }
}
