<?php

namespace Toxicity\AlteredApi\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Toxicity\AlteredApi\Service\CloudflareR2Service;

#[AsCommand(
    name: 'app:image:upload',
    description: 'Download card images and upload them to Cloudflare R2, writing CSV index files.',
)]
class ImageUploadCommand extends Command
{
    private const DB_PATH        = 'community_database';
    private const IMAGES_CSV     = '1_images.csv';
    private const ASSETS_CSV     = '2_assets.csv';
    private const IMAGES_HEADER  = ['reference', 'locale', 'r2_url'];
    private const ASSETS_HEADER  = ['reference', 'asset_url', 'r2_url'];

    public function configure(): void
    {
        $this->addArgument('directory', InputArgument::OPTIONAL, 'Sub-directory to process (e.g. DUSTER/AX/97). Processes all if omitted.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $r2        = new CloudflareR2Service();
        $subDir    = $input->getArgument('directory');
        $basePath  = self::DB_PATH . ($subDir ? '/' . trim($subDir, '/') : '');

        $leafDirs = $this->findLeafDirectories($basePath);

        foreach ($leafDirs as $dir) {
            $this->processDirectory($dir, $r2, $output);
        }

        return Command::SUCCESS;
    }

    private function processDirectory(string $dir, CloudflareR2Service $r2, OutputInterface $output): void
    {
        $jsonFiles = (new Finder())->files()->in($dir)->name('*.json')->depth(0);

        $imageRows = [];
        $assetRows = [];

        // Load existing CSVs to avoid re-uploading already processed entries
        $existingImages = $this->loadCsv($dir . '/' . self::IMAGES_CSV);
        $existingAssets = $this->loadCsv($dir . '/' . self::ASSETS_CSV);

        foreach ($jsonFiles as $file) {
            $data      = json_decode($file->getContents(), true);
            $reference = $data['reference'] ?? pathinfo($file->getFilename(), PATHINFO_FILENAME);

            // --- images (allImagePath, per locale) ---
            foreach ($data['allImagePath'] ?? [] as $locale => $url) {
                if (empty($url)) {
                    continue;
                }

                $key = $this->makeKey($reference, $locale, 'jpg');

                if (isset($existingImages[$reference . '|' . $locale])) {
                    $imageRows[] = $existingImages[$reference . '|' . $locale];
                    continue;
                }

                try {
                    $r2Url = $r2->exists($key) ? $this->buildPublicUrl($key) : $r2->uploadFromUrl($url, $key);
                    $imageRows[] = [$reference, $locale, $r2Url];
                    $output->writeln(sprintf('<info>[image] %s %s → %s</info>', $reference, $locale, $r2Url));
                } catch (\Throwable $e) {
                    $output->writeln(sprintf('<error>[image] %s %s failed: %s</error>', $reference, $locale, $e->getMessage()));
                }
            }

            // --- assets ---
            foreach ($data['assets'] ?? [] as $index => $assetUrl) {
                if (empty($assetUrl)) {
                    continue;
                }

                $assetKey = $this->makeAssetKey($reference, $index);

                if (isset($existingAssets[$reference . '|' . $index])) {
                    $assetRows[] = $existingAssets[$reference . '|' . $index];
                    continue;
                }

                try {
                    $r2Url = $r2->exists($assetKey) ? $this->buildPublicUrl($assetKey) : $r2->uploadFromUrl($assetUrl, $assetKey);
                    $assetRows[] = [$reference, $assetUrl, $r2Url];
                    $output->writeln(sprintf('<info>[asset] %s #%d → %s</info>', $reference, $index, $r2Url));
                } catch (\Throwable $e) {
                    $output->writeln(sprintf('<error>[asset] %s #%d failed: %s</error>', $reference, $index, $e->getMessage()));
                }
            }
        }

        if (!empty($imageRows)) {
            $this->writeCsv($dir . '/' . self::IMAGES_CSV, self::IMAGES_HEADER, $imageRows);
            $output->writeln(sprintf('<comment>Written %s (%d rows)</comment>', $dir . '/' . self::IMAGES_CSV, count($imageRows)));
        }

        if (!empty($assetRows)) {
            $this->writeCsv($dir . '/' . self::ASSETS_CSV, self::ASSETS_HEADER, $assetRows);
            $output->writeln(sprintf('<comment>Written %s (%d rows)</comment>', $dir . '/' . self::ASSETS_CSV, count($assetRows)));
        }
    }

    /**
     * Returns all leaf directories (those that contain JSON files directly).
     * @return string[]
     */
    private function findLeafDirectories(string $basePath): array
    {
        if (!is_dir($basePath)) {
            return [];
        }

        $leaves = [];
        $dirs   = (new Finder())->directories()->in($basePath);

        foreach ($dirs as $dir) {
            $hasJson = (new Finder())->files()->in($dir->getPathname())->name('*.json')->depth(0)->count();
            if ($hasJson > 0) {
                $leaves[] = $dir->getPathname();
            }
        }

        // Also check basePath itself
        $hasJson = (new Finder())->files()->in($basePath)->name('*.json')->depth(0)->count();
        if ($hasJson > 0) {
            $leaves[] = $basePath;
        }

        return array_unique($leaves);
    }

    private function makeKey(string $reference, string $locale, string $ext): string
    {
        return 'cards/' . $reference . '/' . $locale . '.' . $ext;
    }

    private function makeAssetKey(string $reference, int $index): string
    {
        return 'cards/' . $reference . '/asset_' . $index . '.jpg';
    }

    private function buildPublicUrl(string $key): string
    {
        return rtrim(getenv('R2_PUBLIC_URL') ?: '', '/') . '/' . $key;
    }

    private function writeCsv(string $path, array $header, array $rows): void
    {
        $fp = fopen($path, 'w');
        fputcsv($fp, $header);
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    /**
     * Loads an existing CSV and indexes rows by "reference|locale" or "reference|index".
     */
    private function loadCsv(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $indexed = [];
        $fp      = fopen($path, 'r');
        $header  = fgetcsv($fp); // skip header

        while ($row = fgetcsv($fp)) {
            if (count($row) >= 2) {
                $indexed[$row[0] . '|' . $row[1]] = $row;
            }
        }

        fclose($fp);
        return $indexed;
    }
}
