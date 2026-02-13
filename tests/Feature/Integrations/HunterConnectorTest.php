<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
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
    $connector = new HunterConnector;
    $mockClient = new MockClient([
        DomainSearchRequest::class => MockResponse::make([], 200),
    ]);
    $connector->withMockClient($mockClient);

    $request = new DomainSearchRequest(
        domain: 'example.com',
        limit: 10,
        offset: 5,
        type: 'personal',
        sentry: false,
    );

    $connector->send($request);

    $lastRequest = $mockClient->getLastPendingRequest();
    $query = $lastRequest->query()->all();

    expect($query)->toHaveKey('domain', 'example.com')
        ->toHaveKey('limit', 10)
        ->toHaveKey('offset', 5)
        ->toHaveKey('type', 'personal')
        ->toHaveKey('sentry', 'false');
});

it('excludes optional params when not set', function (): void {
    $connector = new HunterConnector;
    $mockClient = new MockClient([
        DomainSearchRequest::class => MockResponse::make([], 200),
    ]);
    $connector->withMockClient($mockClient);

    $request = new DomainSearchRequest('example.com');
    $connector->send($request);

    $lastRequest = $mockClient->getLastPendingRequest();
    $query = $lastRequest->query()->all();

    expect($query)->not->toHaveKey('limit')
        ->not->toHaveKey('offset')
        ->not->toHaveKey('type');
    expect($query)->toHaveKey('sentry', 'true');
});
