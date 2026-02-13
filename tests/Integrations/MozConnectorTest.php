<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\Moz\Data\Requests\LinkingRootDomainsRequestData;
use Seeders\ExternalApis\Integrations\Moz\Data\UrlMetrics\UrlMetricsRequestData;
use Seeders\ExternalApis\Integrations\Moz\MozLinksConnector;
use Seeders\ExternalApis\Integrations\Moz\Requests\LinkingRootDomainsRequest;
use Seeders\ExternalApis\Integrations\Moz\Requests\UrlMetricsRequest;

it('resolves the correct base url', function (): void {
    $connector = new MozLinksConnector;

    expect($connector->resolveBaseUrl())->toBe('https://lsapi.seomoz.com/v2');
});

it('builds url metrics request correctly', function (): void {
    $data = new UrlMetricsRequestData(
        targets: ['example.com', 'test.com'],
    );

    $request = new UrlMetricsRequest($data);

    expect($request->resolveEndpoint())->toBe('/url_metrics');
    expect($request->data->targets)->toBe(['example.com', 'test.com']);
});

it('builds linking root domains request correctly', function (): void {
    $data = new LinkingRootDomainsRequestData(
        target: 'example.com',
    );

    $request = new LinkingRootDomainsRequest($data);

    expect($request->resolveEndpoint())->toBe('/linking_root_domains');
    expect($request->data->target)->toBe('example.com');
    expect($request->data->target_scope)->toBe('page');
    expect($request->data->filter)->toBe('external');
    expect($request->data->limit)->toBe(25);
});
