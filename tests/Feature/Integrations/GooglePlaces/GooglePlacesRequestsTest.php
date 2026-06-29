<?php

declare(strict_types=1);

use Saloon\Http\Request;
use Seeders\ExternalApis\Integrations\GooglePlaces\GooglePlacesConnector;
use Seeders\ExternalApis\Integrations\GooglePlaces\Requests\PlaceDetailsRequest;
use Seeders\ExternalApis\Integrations\GooglePlaces\Requests\TextSearchRequest;

/**
 * @return array<string, mixed>
 */
function placesQuery(Request $request): array
{
    $method = new ReflectionMethod($request, 'defaultQuery');

    return $method->invoke($request);
}

it('resolves the maps base url', function (): void {
    expect((new GooglePlacesConnector)->resolveBaseUrl())->toBe('https://maps.googleapis.com');
});

it('builds the text search endpoint and query', function (): void {
    $request = new TextSearchRequest('Acme Stores');

    expect($request->resolveEndpoint())->toBe('/maps/api/place/textsearch/json')
        ->and(placesQuery($request))->toBe(['query' => 'Acme Stores']);
});

it('builds the place details endpoint and query with default fields', function (): void {
    $request = new PlaceDetailsRequest('place-123');

    expect($request->resolveEndpoint())->toBe('/maps/api/place/details/json')
        ->and(placesQuery($request))->toBe([
            'place_id' => 'place-123',
            'fields' => 'formatted_phone_number,opening_hours,url,website',
        ]);
});
