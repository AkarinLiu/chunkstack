<?php

use Illuminate\Support\Facades\Http;

test('shows message when already on the latest version', function () {
    Http::fake([
        'repo.packagist.org/p2/akarinliu/chunkstack.json' => Http::response([
            'packages' => [
                'akarinliu/chunkstack' => [
                    [
                        'version' => 'v1.1.1',
                        'version_normalized' => '1.1.1.0',
                        'dist' => ['url' => 'https://example.com/release.zip'],
                    ],
                ],
            ],
        ]),
    ]);

    $this->artisan('upgrade')
        ->expectsOutput('Checking for updates...')
        ->expectsOutput('You are already on the latest version (v1.1.1).')
        ->assertExitCode(0);
});

test('shows message when local version is ahead', function () {
    Http::fake([
        'repo.packagist.org/p2/akarinliu/chunkstack.json' => Http::response([
            'packages' => [
                'akarinliu/chunkstack' => [
                    [
                        'version' => 'v1.0.5',
                        'version_normalized' => '1.0.5.0',
                        'dist' => ['url' => 'https://example.com/release.zip'],
                    ],
                ],
            ],
        ]),
    ]);

    $this->artisan('upgrade')
        ->expectsOutput('Checking for updates...')
        ->expectsOutput('Your version (v1.1.1) is ahead of the latest published version (v1.0.5).')
        ->assertExitCode(0);
});

test('shows update available and handles cancellation', function () {
    Http::fake([
        'repo.packagist.org/p2/akarinliu/chunkstack.json' => Http::response([
            'packages' => [
                'akarinliu/chunkstack' => [
                    [
                        'version' => 'v2.0.0',
                        'version_normalized' => '2.0.0.0',
                        'dist' => ['url' => 'https://example.com/release.zip'],
                    ],
                ],
            ],
        ]),
    ]);

    $this->artisan('upgrade')
        ->expectsOutput('Checking for updates...')
        ->expectsOutput('A new version is available!')
        ->expectsConfirmation('Do you want to upgrade?', 'no')
        ->expectsOutput('Upgrade cancelled.')
        ->assertExitCode(0);
});

test('dry run checks but does not upgrade', function () {
    Http::fake([
        'repo.packagist.org/p2/akarinliu/chunkstack.json' => Http::response([
            'packages' => [
                'akarinliu/chunkstack' => [
                    [
                        'version' => 'v2.0.0',
                        'version_normalized' => '2.0.0.0',
                        'dist' => ['url' => 'https://example.com/release.zip'],
                    ],
                ],
            ],
        ]),
    ]);

    $this->artisan('upgrade', ['--dry-run' => true])
        ->expectsOutput('Checking for updates...')
        ->expectsOutput('A new version is available!')
        ->expectsOutput('Dry-run complete. No changes were made.')
        ->assertExitCode(0);
});

test('skips confirmation with force option', function () {
    Http::fake([
        'repo.packagist.org/p2/akarinliu/chunkstack.json' => Http::response([
            'packages' => [
                'akarinliu/chunkstack' => [
                    [
                        'version' => 'v2.0.0',
                        'version_normalized' => '2.0.0.0',
                        'dist' => [
                            'url' => 'https://example.com/release.zip',
                        ],
                    ],
                ],
            ],
        ]),
        'example.com/release.zip' => Http::response('fake-zip-content'),
    ]);

    $this->artisan('upgrade', ['--force' => true])
        ->expectsOutput('Checking for updates...')
        ->expectsOutput('A new version is available!')
        ->expectsOutput('Downloading v2.0.0...')
        ->assertExitCode(1);
});

test('handles API failure gracefully', function () {
    Http::fake([
        'repo.packagist.org/p2/akarinliu/chunkstack.json' => Http::response('', 500),
    ]);

    $this->artisan('upgrade')
        ->expectsOutput('Checking for updates...')
        ->expectsOutput('Failed to fetch version information from Packagist.')
        ->assertExitCode(1);
});

test('handles empty packages response', function () {
    Http::fake([
        'repo.packagist.org/p2/akarinliu/chunkstack.json' => Http::response([
            'packages' => [],
        ]),
    ]);

    $this->artisan('upgrade')
        ->expectsOutput('Checking for updates...')
        ->expectsOutput('Failed to fetch version information from Packagist.')
        ->assertExitCode(1);
});
