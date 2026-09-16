<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\Hunter\HunterConnector;
use Seeders\ExternalApis\Integrations\Hunter\Requests\DomainSearchRequest;

it('resolves the correct base url', function (): void {
    $connector = new HunterConnector;

    expect($connector->resolveBaseUrl())->toBe('https://api.hunter.io/v2');
});

it('builds domain search request with required params', function (): void {
    $request = new DomainSearchRequest('example.com');

    expect($request->resolveEndpoint())->toBe('/domain-search');
});

it('builds domain search query with all optional params', function (): void {
    $request = new DomainSearchRequest(
        domain: 'example.com',
        limit: 10,
        offset: 5,
        type: 'personal',
        sentry: false,
    );

    $query = $request->query()->all();

    expect($query)->toHaveKey('domain', 'example.com')
        ->toHaveKey('limit', 10)
        ->toHaveKey('offset', 5)
        ->toHaveKey('type', 'personal')
        ->toHaveKey('sentry', 'false');
});

it('excludes optional params when not set', function (): void {
    $request = new DomainSearchRequest('example.com');

    $query = $request->query()->all();

    expect($query)->not->toHaveKey('limit')
        ->not->toHaveKey('offset')
        ->not->toHaveKey('type');
    expect($query)->toHaveKey('sentry', 'true');
});

it('sends the api key from config as a default query parameter', function (): void {
    $connector = new HunterConnector;

    expect($connector->query()->all())->toHaveKey('api_key', 'test-hunter-key');
});
