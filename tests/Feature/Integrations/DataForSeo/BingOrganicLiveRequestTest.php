<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\DataForSeo\Requests\Serp\Bing\OrganicLiveAdvancedRequest;

it('builds the Bing organic live advanced endpoint and body', function (): void {
    $payload = [[
        'keyword' => 'site:example.com',
        'location_code' => 2840,
        'language_code' => 'en',
        'depth' => 50,
    ]];

    $request = new OrganicLiveAdvancedRequest($payload);

    expect($request->resolveEndpoint())->toBe('/serp/bing/organic/live/advanced')
        ->and($request->body()->all())->toBe($payload);
});
