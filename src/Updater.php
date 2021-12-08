<?php

declare(strict_types=1);

namespace Eseath\SxGeo;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;

class Updater
{
    protected $client;

    protected $url;

    protected $tmpDir;

    protected $tmpPath;

    protected $dstPath;

    public function __construct(string $url, string $dstPath)
    {
        $this->client = new Client();
        $this->url = $url;
        $this->dstPath = $dstPath;
        $this->tmpDir = sys_get_temp_dir();
    }

    public function getDestinationPath() : string
    {
        return $this->dstPath;
    }

    public function checkForUpdates() : bool
    {
        if (! file_exists($this->dstPath)) {
            return true;
        }

        $response = $this->client->head($this->url);

        $lastModified = new \DateTime($response->getHeaderLine('Last-Modified'));
        $fileModifiedTime = (new \DateTime())->setTimestamp(filemtime($this->dstPath));

        return $lastModified > $fileModifiedTime;
    }

    public function download(callable $progress = null)
    {
        $this->tmpPath = implode(DIRECTORY_SEPARATOR, [$this->tmpDir, 'sypexgeo-' . md5(microtime()) . '.zip']);
        $resource = Utils::tryFopen($this->tmpPath, 'w');

        $this->client->get($this->url, [
            'sink' => $resource,
            'progress' => $progress,
        ]);
    }

    public function extract()
    {
        $extractPath = implode(DIRECTORY_SEPARATOR, [$this->tmpDir, 'sypexgeo-' . md5(microtime())]);

        $zip = new \ZipArchive();
        $res = $zip->open($this->tmpPath);

        if ($res !== true) {
            throw new \Exception("Extraction failed: error code $res");
        }

        $fileName = $zip->getNameIndex(0);

        $zip->extractTo($extractPath);
        $zip->close();

        if (!copy($extractPath . DIRECTORY_SEPARATOR . $fileName, $this->dstPath)) {
            throw new \Exception('Copy failed.');
        }
    }
}
