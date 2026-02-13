<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\SeRanking\Requests\AddSearchEngineRequest;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\CreateKeywordGroupRequest;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\CreateSiteKeywordRequest;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\CreateSiteRequest;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\GetHistoricalDatesRequest;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\GetKeywordGroupRequest;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\GetKeywordRequest;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\GetSitePositionsRequest;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\GetSites;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\GetSiteStat;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\GetSystemSearchEnginesRequest;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\UpdateSiteKeywordRequest;
use Seeders\ExternalApis\Integrations\SeRanking\SeRankingConnector;

it('resolves the correct base url', function (): void {
    $connector = new SeRankingConnector;

    expect($connector->resolveBaseUrl())->toBe('https://api4.seranking.com');
});

it('builds get sites request correctly', function (): void {
    $request = new GetSites;

    expect($request->resolveEndpoint())->toBe('/sites');
});

it('builds get site stat request correctly', function (): void {
    $request = new GetSiteStat(42);

    expect($request->resolveEndpoint())->toBe('/sites/42/stat');
});

it('builds get site positions request correctly', function (): void {
    $request = new GetSitePositionsRequest(42, '2025-01-01', '2025-01-31');

    expect($request->resolveEndpoint())->toBe('/sites/42/positions');
});

it('builds get historical dates request correctly', function (): void {
    $request = new GetHistoricalDatesRequest(42);

    expect($request->resolveEndpoint())->toBe('/sites/42/historicalDates');
});

it('builds get keyword request correctly', function (): void {
    $request = new GetKeywordRequest(42);

    expect($request->resolveEndpoint())->toBe('/sites/42/keywords');
});

it('builds get keyword group request correctly', function (): void {
    $request = new GetKeywordGroupRequest(42);

    expect($request->resolveEndpoint())->toBe('/keyword-groups/42');
});

it('builds get system search engines request correctly', function (): void {
    $request = new GetSystemSearchEnginesRequest;

    expect($request->resolveEndpoint())->toBe('/system/search-engines');
});

it('builds create site request correctly', function (): void {
    $request = new CreateSiteRequest('https://example.com', 'Example Site');

    expect($request->resolveEndpoint())->toBe('/sites');
    expect($request->body()->all())->toBe([
        'url' => 'https://example.com',
        'title' => 'Example Site',
    ]);
});

it('builds add search engine request correctly', function (): void {
    $request = new AddSearchEngineRequest(42);

    expect($request->resolveEndpoint())->toBe('sites/42/search-engines');
    expect($request->body()->all())->toBe([
        'search_engine_id' => 320,
    ]);
});

it('builds create keyword group request correctly', function (): void {
    $request = new CreateKeywordGroupRequest(42, 'Brand Keywords');

    expect($request->resolveEndpoint())->toBe('/keyword-groups');
    expect($request->body()->all())->toBe([
        'name' => 'Brand Keywords',
        'site_id' => 42,
    ]);
});

it('builds create site keyword request correctly', function (): void {
    $request = new CreateSiteKeywordRequest(42, 5, ['laravel', 'php']);

    expect($request->resolveEndpoint())->toBe('/sites/42/keywords');
    expect($request->body()->all())->toBe([
        ['keyword' => 'laravel', 'group_id' => 5],
        ['keyword' => 'php', 'group_id' => 5],
    ]);
});

it('builds update site keyword request correctly', function (): void {
    $request = new UpdateSiteKeywordRequest(42, 99, 'updated keyword');

    expect($request->resolveEndpoint())->toBe('/sites/42/keywords/99');
    expect($request->body()->all())->toBe([
        'keyword' => 'updated keyword',
    ]);
});
