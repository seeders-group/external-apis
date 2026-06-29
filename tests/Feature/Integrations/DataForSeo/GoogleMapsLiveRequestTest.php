<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp\Google\Maps\LiveRequestData;
use Seeders\ExternalApis\Integrations\DataForSeo\Requests\Serp\Google\Maps\OverviewLiveRequest;

it('builds the Google Maps live advanced endpoint from a location code', function (): void {
    $data = new LiveRequestData(
        keyword: 'Spejlshoppen',
        location_code: 2208,
        language_code: 'da',
        depth: 20,
    );

    $request = new OverviewLiveRequest($data);

    expect($request->resolveEndpoint())->toBe('/serp/google/maps/live/advanced')
        ->and($data->keyword)->toBe('Spejlshoppen')
        ->and($data->location_code)->toBe(2208)
        ->and($data->location_name)->toBeNull()
        ->and($data->language_code)->toBe('da')
        ->and($data->depth)->toBe(20);
});

it('still accepts a location name', function (): void {
    $data = new LiveRequestData(
        keyword: 'Spejlshoppen',
        location_name: 'Denmark',
    );

    expect($data->location_name)->toBe('Denmark')
        ->and($data->location_code)->toBeNull();
});
