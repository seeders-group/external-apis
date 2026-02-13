<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Tests;

use Illuminate\Contracts\Config\Repository;
use Orchestra\Testbench\TestCase as Orchestra;
use Seeders\ExternalApis\ExternalApisServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ExternalApisServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app->make(Repository::class)->set('database.default', 'testing');
        $app->make(Repository::class)->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up test environment configuration
        $app->make(Repository::class)->set('external-apis.openai.key', 'test-key');
        $app->make(Repository::class)->set('external-apis.gemini.key', 'test-gemini-key');
        $app->make(Repository::class)->set('external-apis.ahrefs.token', 'test-token');
        $app->make(Repository::class)->set('external-apis.dataforseo.username', 'test-user');
        $app->make(Repository::class)->set('external-apis.dataforseo.password', 'test-pass');
        $app->make(Repository::class)->set('external-apis.moz.client_id', 'test-moz-client-id');
        $app->make(Repository::class)->set('external-apis.moz.client_secret', 'test-moz-client-secret');
        $app->make(Repository::class)->set('external-apis.semrush.api_key', 'test-semrush-key');
        $app->make(Repository::class)->set('external-apis.hunter.api_key', 'test-hunter-key');
        $app->make(Repository::class)->set('external-apis.majestic.api_key', 'test-majestic-key');
        $app->make(Repository::class)->set('external-apis.seranking.token', 'test-seranking-token');
        $app->make(Repository::class)->set('external-apis.scraperapi.key', 'test-scraperapi-key');
        $app->make(Repository::class)->set('external-apis.google_search.key', 'test-google-search-key');
        $app->make(Repository::class)->set('external-apis.google_search.cx', 'test-google-search-cx');
    }
}
