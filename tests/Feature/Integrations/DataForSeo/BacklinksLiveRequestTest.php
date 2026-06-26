<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\DataForSeo\Requests\Backlinks\BacklinksLiveRequest;
use Seeders\ExternalApis\Integrations\DataForSeo\Requests\Backlinks\SummaryLiveRequest;

it('builds the backlinks summary live endpoint and body', function (): void {
    $payload = [['target' => 'example.com', 'include_subdomains' => true]];

    $request = new SummaryLiveRequest($payload);

    expect($request->resolveEndpoint())->toBe('/backlinks/summary/live')
        ->and($request->body()->all())->toBe($payload);
});

it('builds the backlinks live endpoint and body', function (): void {
    $payload = [['target' => 'example.com', 'limit' => 100, 'mode' => 'as_is']];

    $request = new BacklinksLiveRequest($payload);

    expect($request->resolveEndpoint())->toBe('/backlinks/backlinks/live')
        ->and($request->body()->all())->toBe($payload);
});
