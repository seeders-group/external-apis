<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Tests;

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
        // Set up test environment configuration
        $app['config']->set('external-apis.openai.key', 'test-key');
        $app['config']->set('external-apis.gemini.key', 'test-gemini-key');
        $app['config']->set('external-apis.ahrefs.token', 'test-token');
        $app['config']->set('external-apis.dataforseo.username', 'test-user');
        $app['config']->set('external-apis.dataforseo.password', 'test-pass');
        $app['config']->set('external-apis.moz.client_id', 'test-moz-client-id');
        $app['config']->set('external-apis.moz.client_secret', 'test-moz-client-secret');
        $app['config']->set('external-apis.semrush.api_key', 'test-semrush-key');
    }
}
