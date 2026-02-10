<?php

declare(strict_types=1);

use Seeders\ExternalApis\Connectors\Semrush\Requests\ApiUnitsBalanceRequest;
use Seeders\ExternalApis\Connectors\Semrush\Requests\BacklinksOverviewRequest;
use Seeders\ExternalApis\Connectors\Semrush\Requests\BatchComparisonRequest;
use Seeders\ExternalApis\Connectors\Semrush\SemrushConnector;

it('resolves the correct base url', function () {
    $connector = new SemrushConnector;

    expect($connector->resolveBaseUrl())->toBe('https://api.semrush.com');
});

it('builds backlinks overview query correctly', function () {
    $request = new BacklinksOverviewRequest(
        target: 'example.com',
        targetType: 'root_domain',
        database: 'us',
        exportColumns: 'domain,ascore,backlinks',
        displayLimit: 10,
        displayOffset: 0,
    );

    $reflection = new ReflectionClass($request);
    $method = $reflection->getMethod('defaultQuery');
    $method->setAccessible(true);

    $query = $method->invoke($request);

    expect($request->resolveEndpoint())->toBe('/analytics/v1/');
    expect($query)->toMatchArray([
        'type' => 'backlinks_overview',
        'target' => 'example.com',
        'target_type' => 'root_domain',
        'database' => 'us',
        'export_columns' => 'domain,ascore,backlinks',
        'api_key' => 'test-semrush-key',
        'display_limit' => 10,
        'display_offset' => 0,
    ]);
});

it('builds batch comparison query correctly', function () {
    $request = new BatchComparisonRequest(
        targets: ['example.com', 'example.org'],
        targetTypes: ['root_domain', 'root_domain'],
        database: 'us',
        exportColumns: 'domain,ascore,backlinks',
        displayLimit: 20,
        displayOffset: 5,
    );

    $reflection = new ReflectionClass($request);
    $method = $reflection->getMethod('defaultQuery');
    $method->setAccessible(true);

    $query = $method->invoke($request);

    expect($request->resolveEndpoint())->toBe('/analytics/v1/');
    expect($query)->toMatchArray([
        'type' => 'backlinks_comparison',
        'targets' => 'example.com,example.org',
        'target_types' => 'root_domain,root_domain',
        'database' => 'us',
        'export_columns' => 'domain,ascore,backlinks',
        'api_key' => 'test-semrush-key',
        'display_limit' => 20,
        'display_offset' => 5,
    ]);
});

it('rejects batch comparison when target counts mismatch', function () {
    expect(fn () => new BatchComparisonRequest(
        targets: ['example.com', 'example.org'],
        targetTypes: ['root_domain'],
        database: 'us',
        exportColumns: 'domain,ascore,backlinks',
    ))->toThrow(InvalidArgumentException::class);
});

it('builds api units balance query correctly', function () {
    $request = new ApiUnitsBalanceRequest;

    $reflection = new ReflectionClass($request);
    $method = $reflection->getMethod('defaultQuery');
    $method->setAccessible(true);

    $query = $method->invoke($request);

    expect($request->resolveEndpoint())->toBe('/');
    expect($query)->toMatchArray([
        'type' => 'api_units',
        'key' => 'test-semrush-key',
    ]);
});
