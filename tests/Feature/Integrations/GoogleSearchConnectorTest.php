<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\GoogleSearch\GoogleSearchConnector;
use Seeders\ExternalApis\Integrations\GoogleSearch\Requests\CustomSearchRequest;

it('resolves the correct base url', function (): void {
    $connector = new GoogleSearchConnector;

    expect($connector->resolveBaseUrl())->toBe('https://www.googleapis.com');
});

it('builds custom search request correctly', function (): void {
    $request = new CustomSearchRequest('laravel framework');

    expect($request->resolveEndpoint())->toBe('/customsearch/v1');
});

it('sends the search query without a country when none is given', function (): void {
    $request = new CustomSearchRequest('laravel framework');

    expect($request->query()->all())->toBe(['q' => 'laravel framework']);
});

it('sends the country as the gl parameter when given', function (): void {
    $request = new CustomSearchRequest('laravel framework', 'nl');

    expect($request->query()->all())->toBe([
        'q' => 'laravel framework',
        'gl' => 'nl',
    ]);
});
