<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Process;
use ZipArchive;

class UpgradeCommand extends Command
{
    protected $signature = 'upgrade {--force : Do not ask for confirmation} {--dry-run : Check for updates without performing upgrade}';

    protected $description = 'Upgrade ChunkStack to the latest version from Packagist';

    public function handle(): int
    {
        $this->info('Checking for updates...');

        $latest = $this->fetchLatestVersion();

        if ($latest === null) {
            $this->error('Failed to fetch version information from Packagist.');

            return self::FAILURE;
        }

        $current = $this->getCurrentVersion();

        $comparison = version_compare(
            $this->normalizeVersion($latest['version']),
            $this->normalizeVersion($current['version'])
        );

        if ($comparison < 0) {
            $this->info("Your version ({$current['version']}) is ahead of the latest published version ({$latest['version']}).");

            return self::SUCCESS;
        }

        if ($comparison === 0) {
            $this->info("You are already on the latest version ({$latest['version']}).");

            return self::SUCCESS;
        }

        $this->newLine();
        $this->warn('A new version is available!');
        $this->line('  Current: <fg=red>'.$current['version'].'</>');
        $this->line('  Latest:  <fg=green>'.$latest['version'].'</>');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info('Dry-run complete. No changes were made.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('Do you want to upgrade?', false)) {
            $this->info('Upgrade cancelled.');

            return self::SUCCESS;
        }

        return $this->performUpgrade($latest);
    }

    protected function fetchLatestVersion(): ?array
    {
        try {
            $response = Http::timeout(30)
                ->get('https://repo.packagist.org/p2/akarinliu/chunkstack.json');

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            $packages = $data['packages']['akarinliu/chunkstack'] ?? [];

            if (empty($packages)) {
                return null;
            }

            return $packages[0];
        } catch (\Exception) {
            return null;
        }
    }

    protected function getCurrentVersion(): array
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        $version = $composer['version'] ?? '0.0.0';

        return [
            'version' => $version,
            'version_normalized' => ltrim($version, 'v'),
        ];
    }

    protected function normalizeVersion(string $version): string
    {
        $version = ltrim($version, 'v');
        $parts = explode('.', $version);

        while (count($parts) < 4) {
            $parts[] = '0';
        }

        return implode('.', $parts);
    }

    protected function performUpgrade(array $latest): int
    {
        $this->info("Downloading {$latest['version']}...");

        $tmpDir = storage_path('app/tmp/chunkstack-upgrade');
        $zipPath = $tmpDir.'/release.zip';
        $extractPath = $tmpDir.'/extracted';

        if (! is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        try {
            $response = Http::timeout(300)->get($latest['dist']['url']);

            if (! $response->successful()) {
                $this->error('Failed to download the release archive.');

                return self::FAILURE;
            }

            file_put_contents($zipPath, $response->body());
        } catch (\Exception $e) {
            $this->error('Download failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Extracting...');

        $zip = new ZipArchive;

        if ($zip->open($zipPath) !== true) {
            $this->error('Failed to open the release archive.');

            return self::FAILURE;
        }

        if (is_dir($extractPath)) {
            $this->deleteDirectory($extractPath);
        }
        mkdir($extractPath, 0755, true);

        $zip->extractTo($extractPath);
        $zip->close();

        $subdirs = glob($extractPath.'/*', GLOB_ONLYDIR);
        $sourcePath = count($subdirs) === 1 ? $subdirs[0] : $extractPath;

        $this->info('Updating project files...');
        $this->copyProjectFiles($sourcePath, base_path());

        $this->info('Installing dependencies...');
        $exitCode = $this->runComposerInstall();

        if ($exitCode !== 0) {
            $this->warn('Composer install completed with warnings. Please check the output above.');
        }

        $this->info('Running migrations...');
        $this->call('migrate', ['--force' => true]);

        $this->call('optimize:clear');

        $this->deleteDirectory($tmpDir);

        $this->newLine();
        $this->info('Upgrade complete! ChunkStack has been upgraded to '.$latest['version'].'.');

        return self::SUCCESS;
    }

    protected function copyProjectFiles(string $source, string $dest): void
    {
        $excludeDirs = ['vendor', 'node_modules', 'storage', '.git'];
        $excludePrefixes = ['.env'];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $sourceLen = strlen($source) + 1;

        foreach ($iterator as $item) {
            $relativePath = substr($item->getPathname(), $sourceLen);

            if ($relativePath === '' || $relativePath === false) {
                continue;
            }

            $topDir = explode(DIRECTORY_SEPARATOR, $relativePath, 2)[0];

            if (in_array($topDir, $excludeDirs, true)) {
                continue;
            }

            if (str_starts_with(basename($relativePath), '.env')) {
                continue;
            }

            $target = $dest.DIRECTORY_SEPARATOR.$relativePath;

            if ($item->isDir()) {
                if (! is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                $targetDir = dirname($target);
                if (! is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                copy($item->getPathname(), $target);
            }
        }
    }

    protected function runComposerInstall(): int
    {
        $process = new Process(['composer', 'install', '--no-interaction'], base_path());
        $process->setTimeout(600);

        $process->run(function (string $type, string $buffer): void {
            $this->output->write($buffer);
        });

        return $process->getExitCode();
    }

    protected function deleteDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
        }

        rmdir($path);
    }
}
