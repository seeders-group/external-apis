<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Seeders\ExternalApis\Connectors\Ahrefs\AhrefsConnector;
use Seeders\ExternalApis\Connectors\Ahrefs\Requests\SiteExplorer\DomainRatingRequest;

it('can send a domain rating request', function (): void {
    $mockClient = new MockClient([
        DomainRatingRequest::class => MockResponse::make([
            'domain_rating' => [
                'domain_rating' => 75.5,
            ],
        ], 200),
    ]);

    $connector = new AhrefsConnector;
    $connector->withMockClient($mockClient);

    $response = $connector->send(new DomainRatingRequest('example.com'));

    expect($response->successful())->toBeTrue();
    expect($response->json('domain_rating.domain_rating'))->toBe(75.5);
});

it('resolves the correct base url', function (): void {
    $connector = new AhrefsConnector;

    expect($connector->resolveBaseUrl())->toBe('https://api.ahrefs.com/v3');
});
