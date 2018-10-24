<?php
namespace Eseath\SxGeo\Facades;

use Illuminate\Support\Facades\Facade;

class SxGeo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() : string
    {
        return Eseath\SxGeo\SxGeo::class;
    }
}
