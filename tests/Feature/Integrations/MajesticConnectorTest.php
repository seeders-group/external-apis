<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Seeders\ExternalApis\Integrations\Majestic\MajesticConnector;
use Seeders\ExternalApis\Integrations\Majestic\Requests\GetIndexItemInfo;

it('resolves the correct base url', function (): void {
    $connector = new MajesticConnector;

    expect($connector->resolveBaseUrl())->toBe('https://api.majestic.com/api/json');
});

it('builds get index item info request correctly', function (): void {
    $request = new GetIndexItemInfo('example.com');

    expect($request->resolveEndpoint())->toBe('/');
});

it('strips protocol from domain in query', function (): void {
    $connector = new MajesticConnector;
    $mockClient = new MockClient([
        GetIndexItemInfo::class => MockResponse::make([], 200),
    ]);
    $connector->withMockClient($mockClient);

    $connector->send(new GetIndexItemInfo('https://example.com'));

    $lastRequest = $mockClient->getLastPendingRequest();
    $query = $lastRequest->query()->all();

    expect($query)->toHaveKey('item0', 'example.com')
        ->toHaveKey('cmd', 'GetIndexItemInfo')
        ->toHaveKey('datasource', 'fresh')
        ->toHaveKey('items', 1);
});
