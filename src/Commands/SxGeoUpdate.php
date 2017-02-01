<?php
namespace Eseath\SxGeo\Commands;

use ZipArchive;
use Illuminate\Console\Command;

class SxGeoUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sxgeo:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating geodatabase';

    /**
     * The URL of the database file.
     *
     * @var string
     */
    private $dbFileURL;

    /**
     * The local path to the database file.
     *
     * @var string
     */
    private $localPath;

    /**
     * The temporary directory.
     *
     * @var string
     */
    private $tmpDir;

    /**
     * @var string
     */
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36';

    /**
     * SxGeoUpdate constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->dbFileURL = config('sxgeo.dbFileURL');
        $this->localPath = config('sxgeo.localPath');
        $this->tmpDir    = sys_get_temp_dir();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->checkForUpdates()) {
            $this->download();
        } else {
            $this->info('No updates available.');
        }
    }

    /**
     * Checks for updates.
     *
     * @return bool
     */
    private function checkForUpdates()
    {
        $this->info('Checking for updates...');

        if (!file_exists($this->localPath)) {
            return true;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $this->dbFileURL);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $content = curl_exec($ch);
        $headers = explode("\r\n", trim($content));
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        array_shift($headers);

        $response = [
            'status'  => $http_status,
            'headers' => []
        ];

        foreach ($headers as $header) {
            list($x, $y) = explode(': ', $header);
            $response['headers'][$x] = $y;
        }

        $lastModified = new \DateTime($response['headers']['Last-Modified']);
        $fileModifiedTime = (new \DateTime())->setTimestamp(filemtime($this->localPath));

        return $lastModified > $fileModifiedTime;
    }

    /**
     * Retrieves the SxGeo database file.
     *
     * @return void
     */
    private function download()
    {
        $this->info('Downloading database file...');

        $zipFile = implode(DIRECTORY_SEPARATOR, [
            $this->tmpDir,
            'sypexgeo-' . md5(microtime()) . '.zip',
        ]);

        $zipResource = fopen($zipFile, "w");

        $progressBar = $this->output->createProgressBar();
        $progressBar->setFormatDefinition('custom', 'Downloaded %now%Mb/%total%Mb (%dlPercent%%)');
        $progressBar->setFormat('custom');
        $progressBar->start();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->dbFileURL);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function ($resource, $totalBytes, $downloadedBytes) use ($progressBar, &$last) {
            if ($totalBytes !== 0) {
                $totalMb = number_format($totalBytes / (1024 * 1024), 2);
                $downloadedMb = number_format($downloadedBytes / (1024 * 1024), 2);

                if ($last !== $downloadedMb) {
                    $progressBar->setMessage($totalMb, 'total');
                    $progressBar->setMessage($downloadedMb, 'now');
                    $progressBar->setMessage(intval($downloadedMb / ($totalMb / 100)), 'dlPercent');
                    $progressBar->advance();
                    $last = $downloadedMb;
                } else {
                    $progressBar->finish();
                }
            }
        });
        curl_setopt($ch, CURLOPT_FILE, $zipResource);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->output->newLine();

        if (!$result) {
            $this->error('Download failed: ' . curl_error($ch));
            return;
        }

        $this->extract($zipFile);
    }

    /**
     * @param  string  $archivePath
     * @return void
     */
    private function extract($archivePath)
    {
        $this->info('Extracting file from archive...');


        $extractPath = implode(DIRECTORY_SEPARATOR, [$this->tmpDir, 'sypexgeo-' . md5(microtime())]);

        $zip = new ZipArchive();
        $res = $zip->open($archivePath);

        if ($res !== true) {
            $this->error("Extraction failed: error code {$res}");
            return;
        }

        $fileName = $zip->getNameIndex(0);

        $zip->extractTo($extractPath);
        $zip->close();

        $this->info("Copying file to {$this->localPath}...");

        if (!copy($extractPath . DIRECTORY_SEPARATOR . $fileName, $this->localPath)) {
            $this->error('Copy failed.');
            return;
        }

        $this->info('Updating complete.');
    }
}
