<?php

declare(strict_types=1);

use Seeders\ExternalApis\Connectors\DataForSeo\DataForSeoConnector;

it('resolves the correct base url', function () {
    $connector = new DataForSeoConnector;

    expect($connector->resolveBaseUrl())->toBe('https://api.dataforseo.com/v3');
});

it('has correct timeout settings', function () {
    $connector = new DataForSeoConnector;

    $reflection = new ReflectionClass($connector);

    $connectTimeout = $reflection->getProperty('connectTimeout');
    $connectTimeout->setAccessible(true);

    $requestTimeout = $reflection->getProperty('requestTimeout');
    $requestTimeout->setAccessible(true);

    expect($connectTimeout->getValue($connector))->toBe(60);
    expect($requestTimeout->getValue($connector))->toBe(120);
});
