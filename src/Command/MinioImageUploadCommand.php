<?php

namespace Toxicity\AlteredApi\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Toxicity\AlteredApi\Service\MinioService;

/**
 * Structure MinIO produite :
 *
 *   {SET}/{FACTION}/{NUMBER}/
 *     1_{assetname}.ext          ← assets partagés (identiques pour tous les R1, R2… du même numéro)
 *     2_{assetname}.ext
 *     {REFERENCE}/               ← un sous-dossier par carte unique
 *       {REFERENCE}_{locale}.ext
 *       {REFERENCE}_{locale}.ext
 *
 * Variables d'environnement requises :
 *   MINIO_ENDPOINT   – ex. http://nas.local:9000
 *   MINIO_ACCESS_KEY
 *   MINIO_SECRET_KEY
 *   MINIO_BUCKET     – défaut : altered-images
 *   MINIO_PUBLIC_URL – optionnel, pour construire l'URL publique
 */
#[AsCommand(
    name: 'app:minio:image:upload',
    description: 'Download card images from JSON files and upload them to a MinIO NAS with structured paths.',
)]
class MinioImageUploadCommand extends Command
{
    private const DB_PATH = 'community_database';

    public function configure(): void
    {
        $this->addArgument(
            'directory',
            InputArgument::OPTIONAL,
            'Sub-directory to process (e.g. DUSTER/AX/97). Processes all if omitted.'
        );
        $this->addOption('batch-size', null, InputArgument::OPTIONAL, 'Directories per batch (default: process all at once).', 100);
        $this->addOption('offset',     null, InputArgument::OPTIONAL, 'Number of directories to skip before starting.', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $minio    = new MinioService();
        $subDir   = $input->getArgument('directory');
        $basePath = self::DB_PATH . ($subDir ? '/' . trim($subDir, '/') : '');

        if (!is_dir($basePath)) {
            $output->writeln(sprintf('<error>No JSON directories found under "%s".</error>', $basePath));
            return Command::FAILURE;
        }

        $offset    = max(0, (int) $input->getOption('offset'));
        $batchSize = max(1, (int) $input->getOption('batch-size'));
        $skipped   = 0;
        $processed = 0;

        $output->writeln(sprintf('<comment>Scanning %s (offset %d, batch size %d)…</comment>', $basePath, $offset, $batchSize));

        $progress = new ProgressBar($output);
        $progress->setFormat(' %current% dirs | %elapsed:6s% elapsed | %message%');
        $progress->setMessage('starting…');
        $progress->start();

        foreach ($this->iterateLeafDirectories($basePath) as $dir) {
            if ($skipped < $offset) {
                $skipped++;
                continue;
            }

            $progress->setMessage(basename(dirname($dir)) . '/' . basename($dir));
            $this->processDirectory($dir, $minio, $output, $progress);
            $progress->advance();
            $processed++;
        }

        $progress->setMessage('done');
        $progress->finish();
        $output->writeln('');
        $output->writeln(sprintf('<info>%d directories processed.</info>', $processed));

        return Command::SUCCESS;
    }

    private function processDirectory(string $dir, MinioService $minio, OutputInterface $output, ?ProgressBar $progress = null): void
    {
        // With a progress bar active, only print details in verbose mode to avoid clashing output
        $log = function (string $msg) use ($output, $progress): void {
            if (!$output->isVerbose()) {
                return;
            }
            if ($progress) {
                $progress->clear();
            }
            $output->writeln($msg);
            if ($progress) {
                $progress->display();
            }
        };
        // Build the MinIO folder prefix by stripping 'community_database/' from the path
        $folderPrefix = preg_replace('#^' . preg_quote(self::DB_PATH, '#') . '/#', '', $dir);

        $jsonFiles = (new Finder())->files()->in($dir)->name('*.json')->depth(0)->sortByName();

        // First pass: collect card data and deduplicate assets by URL
        $cards     = []; // [reference => [locale => url]]
        $allAssets = []; // unique asset URLs shared across all cards of same number

        foreach ($jsonFiles as $file) {
            $data      = json_decode($file->getContents(), true);
            $reference = $data['reference'] ?? pathinfo($file->getFilename(), PATHINFO_FILENAME);

            $cards[$reference] = $data['allImagePath'] ?? [];

            // assets = { "WEB": ["url1", "url2", ...], "TYPE2": [...], ... }
            foreach ($data['assets'] ?? [] as $urls) {
                foreach ((array) $urls as $assetUrl) {
                    if (!empty($assetUrl) && !in_array($assetUrl, $allAssets, true)) {
                        $allAssets[] = $assetUrl;
                    }
                }
            }
        }

        // Upload assets to the NUMBER-level folder (1_name.ext, 2_name.ext, …)
        $position = 1;
        foreach ($allAssets as $assetUrl) {
            $assetName = basename(parse_url($assetUrl, PHP_URL_PATH));
            $key       = $folderPrefix . '/' . $position . '_' . $assetName;

            if ($minio->exists($key)) {
                $log(sprintf('<comment>[asset] skip  %s (already exists)</comment>', $key));
            } else {
                try {
                    $publicUrl = $minio->uploadFromUrl($assetUrl, $key);
                    $log(sprintf('<info>[asset] %s → %s</info>', $key, $publicUrl));
                } catch (\Throwable $e) {
                    $log(sprintf('<error>[asset] %s failed: %s</error>', $key, $e->getMessage()));
                }
            }

            $position++;
        }

        // Upload locale images to per-reference sub-folders
        foreach ($cards as $reference => $locales) {
            foreach ($locales as $locale => $imageUrl) {
                if (empty($imageUrl)) {
                    continue;
                }

                $ext = $this->extractExtension($imageUrl);
                $key = $folderPrefix . '/' . $reference . '/' . $reference . '_' . $locale . '.' . $ext;

                if ($minio->exists($key)) {
                    $log(sprintf('<comment>[image] skip  %s (already exists)</comment>', $key));
                    continue;
                }

                try {
                    $publicUrl = $minio->uploadFromUrl($imageUrl, $key);
                    $log(sprintf('<info>[image] %s → %s</info>', $key, $publicUrl));
                } catch (\Throwable $e) {
                    $log(sprintf('<error>[image] %s %s failed: %s</error>', $reference, $locale, $e->getMessage()));
                }
            }
        }
    }

    /**
     * Yields unique directories containing JSON files, using native PHP iterators (no Finder overhead).
     */
    private function iterateLeafDirectories(string $basePath): \Generator
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $seen = [];
        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if ($file->getExtension() === 'json') {
                $dir = $file->getPath();
                if (!isset($seen[$dir])) {
                    $seen[$dir] = true;
                    yield $dir;
                }
            }
        }
    }

    private function extractExtension(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $ext  = strtolower(pathinfo($path ?? '', PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? $ext : 'jpg';
    }
}
