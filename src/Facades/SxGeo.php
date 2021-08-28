<?php
namespace Eseath\SxGeo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static int getCountryId(string $ip)
 * @method static string getCountry(string $ip)
 * @method static array|false get(string $ip)
 * @method static array|false getCity(string $ip)
 * @method static array|false getCityFull(string $ip)
 * @method static array about()
 */
class SxGeo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() : string
    {
        return \Eseath\SxGeo\SxGeo::class;
    }
}
