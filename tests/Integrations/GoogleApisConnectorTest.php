<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\GoogleApis\GoogleApisConnector;
use Seeders\ExternalApis\Integrations\GoogleApis\Requests\PageSpeedOnlineRequest;

it('resolves the correct base url', function (): void {
    $connector = new GoogleApisConnector;

    expect($connector->resolveBaseUrl())->toBe('https://www.googleapis.com');
});

it('builds pagespeed request with correct endpoint', function (): void {
    config()->set('external-apis.google_pagespeed.key', 'test-pagespeed-key');

    $request = new PageSpeedOnlineRequest('https://example.com');

    $endpoint = $request->resolveEndpoint();

    expect($endpoint)->toStartWith('/pagespeedonline/v5/runPagespeed?')
        ->toContain('url=')
        ->toContain('strategy=mobile')
        ->toContain('category=performance')
        ->toContain('category=seo')
        ->toContain('category=accessibility')
        ->toContain('category=best-practices');
});
