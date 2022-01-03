<?php

declare(strict_types=1);

namespace Eseath\SxGeo\Tests;

use Eseath\SxGeo\Facades\SxGeo;

class FacadeTest extends TestCase
{
    public function testMetadata() : void
    {
        config()->set('sxgeo.localPath', __DIR__ . '/.assets/SxGeoCountry.dat');

        $metadata = SxGeo::getMetadata();

        $this->assertSame('2.2', $metadata['Version']);
        $this->assertSame('latin1', $metadata['Charset']);
    }
}
