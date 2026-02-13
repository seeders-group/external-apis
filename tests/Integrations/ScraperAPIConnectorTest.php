<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\ScraperAPI\Requests\GoogleSearchRequest;
use Seeders\ExternalApis\Integrations\ScraperAPI\Requests\ScrapeRequest;
use Seeders\ExternalApis\Integrations\ScraperAPI\ScraperAPIConnector;

it('resolves the correct base url', function (): void {
    $connector = new ScraperAPIConnector;

    expect($connector->resolveBaseUrl())->toBe('http://api.scraperapi.com');
});

it('has correct timeout settings', function (): void {
    $connector = new ScraperAPIConnector;
    $reflection = new ReflectionClass($connector);

    $connectTimeout = $reflection->getProperty('connectTimeout');
    $requestTimeout = $reflection->getProperty('requestTimeout');

    expect($connectTimeout->getValue($connector))->toBe(10);
    expect($requestTimeout->getValue($connector))->toBe(120);
});

it('builds scrape request correctly', function (): void {
    $request = new ScrapeRequest('https://example.com');

    expect($request->resolveEndpoint())->toBe('');
});

it('builds google search request correctly', function (): void {
    $request = new GoogleSearchRequest('laravel php');

    expect($request->resolveEndpoint())->toBe('/structured/google/search');
});
