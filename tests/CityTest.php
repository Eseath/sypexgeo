<?php

namespace Eseath\SxGeo\Tests;

use PHPUnit\Framework\Constraint\IsType;
use Eseath\SxGeo\SxGeo;

class CityTest extends TestCase
{
    /**
     * @var SxGeo
     */
    protected $base;

    /**
     * IP-address info.
     * 
     * @var array
     */
    protected $data;

    public function setUp()
    {
        parent::setUp();

        $this->base = $this->app->make(SxGeo::class);
        $this->data = $this->base->getCityFull('5.255.255.5');
    }

    public function testCountry()
    {
        $this->assertEquals('RU', $this->data['country']['iso']);
        $this->assertEquals('Russia', $this->data['country']['name_en']);
        $this->assertEquals('Россия', $this->data['country']['name_ru']);
    }

    public function testRegion()
    {
        $this->assertEquals('RU-MOW', $this->data['region']['iso']);
        $this->assertEquals('Moskva', $this->data['region']['name_en']);
        $this->assertEquals('Москва', $this->data['region']['name_ru']);
    }

    public function testCity()
    {
        $this->assertEquals('Moscow', $this->data['city']['name_en']);
        $this->assertEquals('Москва', $this->data['city']['name_ru']);
    }
}
