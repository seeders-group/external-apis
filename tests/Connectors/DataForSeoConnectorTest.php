<?php

declare(strict_types=1);

use Seeders\ExternalApis\Connectors\DataForSeo\DataForSeoConnector;

it('resolves the correct base url', function (): void {
    $connector = new DataForSeoConnector;

    expect($connector->resolveBaseUrl())->toBe('https://api.dataforseo.com/v3');
});

it('has correct timeout settings', function (): void {
    $connector = new DataForSeoConnector;

    $reflection = new ReflectionClass($connector);

    $connectTimeout = $reflection->getProperty('connectTimeout');

    $requestTimeout = $reflection->getProperty('requestTimeout');

    expect($connectTimeout->getValue($connector))->toBe(60);
    expect($requestTimeout->getValue($connector))->toBe(120);
});
