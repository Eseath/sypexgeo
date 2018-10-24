<?php

namespace Eseath\SxGeo\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Eseath\SxGeo\SxGeoServiceProvider;

class TestCase extends OrchestraTestCase
{
    /**
     * Set the package service provider.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app) : array
    {
        return [SxGeoServiceProvider::class];
    }
}
