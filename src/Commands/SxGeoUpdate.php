<?php

declare(strict_types=1);

namespace Eseath\SxGeo\Commands;

use Eseath\SxGeo\Updater;
use Illuminate\Console\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class SxGeoUpdate extends Command
{
    protected $signature = 'sxgeo:update
                                {--force : Do not check SypexGeo database timestamp}';

    protected $description = 'Updates the SypexGeo database or installs it if it does not exist.';

    protected $updater;

    public function __construct()
    {
        parent::__construct();

        $this->updater = new Updater(config('sxgeo.dbFileURL'), config('sxgeo.localPath'));
    }

    public function handle()
    {
        $this->output->getFormatter()->setStyle('title', new OutputFormatterStyle('blue', null, ['bold']));
        $this->output->newLine();

        if (! $this->option('force') && ! $this->checkForUpdates()) {
            return;
        }

        $this->download();

        if (! $this->extract()) {
            return;
        }

        $this->info(PHP_EOL . 'The SypexGeo database successfully updated.');
        $this->output->newLine();
    }

    protected function checkForUpdates()
    {
        $this->output->writeln('<title>• Checking for updates...</title>');

        if (! $this->updater->checkForUpdates()) {
            $this->info('  No updates available.');
            $this->output->newLine();
            return false;
        }

        $this->info('  Updates available!');

        return true;
    }

    protected function download()
    {
        $this->output->writeln('<title>• Downloading database file...</title>');

        $progressBar = $this->output->createProgressBar();
        $progressBar->setFormatDefinition('custom', '  <fg=green>Downloaded %now%Mb/%total%Mb (%dlPercent%%)</>');
        $progressBar->setFormat('custom');
        $progressBar->start();

        $this->updater->download(function (int $totalBytes, int $downloadedBytes) use ($progressBar) {
            if ($totalBytes !== 0) {
                $totalMb = number_format($totalBytes / (1024 * 1024), 2);
                $downloadedMb = number_format($downloadedBytes / (1024 * 1024), 2);

                if ($downloadedBytes < $totalBytes) {
                    $progressBar->setMessage($totalMb, 'total');
                    $progressBar->setMessage($downloadedMb, 'now');
                    $progressBar->setMessage((string) (int) ($downloadedMb / ($totalMb / 100)), 'dlPercent');
                    $progressBar->advance();
                } else {
                    $progressBar->finish();
                }
            }
        });

        $this->output->newLine();
    }

    protected function extract()
    {
        $this->output->writeln('<title>• Extracting file from archive...</title>');

        try {
            $this->updater->extract();
            $this->info('  File copied to ' . $this->updater->getDestinationPath());
        } catch (\Exception $e) {
            $this->output->writeln('  <fg=red>' . $e->getMessage() . '</>');
            $this->output->writeln('  <fg=red>' . $e->getFile() . '#' . $e->getLine() . '</>');
            $this->output->newLine();
            return false;
        }

        return true;
    }
}
