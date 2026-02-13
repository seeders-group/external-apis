<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Seeders\ExternalApis\Connectors\Semrush\Requests\ApiUnitsBalanceRequest;
use Seeders\ExternalApis\Connectors\Semrush\Requests\BacklinksOverviewRequest;
use Seeders\ExternalApis\Connectors\Semrush\Requests\BatchComparisonRequest;
use Seeders\ExternalApis\Connectors\Semrush\SemrushConnector;
use Seeders\ExternalApis\Data\Semrush\ApiUnitsBalanceResponseData;
use Seeders\ExternalApis\Data\Semrush\BacklinksOverviewResponseData;
use Seeders\ExternalApis\Data\Semrush\BatchComparisonResponseData;

it('resolves the correct base url', function (): void {
    $connector = new SemrushConnector;

    expect($connector->resolveBaseUrl())->toBe('https://api.semrush.com');
});

it('builds backlinks overview query correctly', function (): void {
    $request = new BacklinksOverviewRequest(
        target: 'example.com',
        targetType: 'root_domain',
        exportColumns: 'ascore,total,domains_num',
        displayLimit: 10,
        displayOffset: 0,
    );

    $reflection = new ReflectionClass($request);
    $method = $reflection->getMethod('defaultQuery');

    $query = $method->invoke($request);

    expect($request->resolveEndpoint())->toBe('/analytics/v1/');
    expect($query)->toMatchArray([
        'type' => 'backlinks_overview',
        'target' => 'example.com',
        'target_type' => 'root_domain',
        'export_columns' => 'ascore,total,domains_num',
        'key' => 'test-semrush-key',
    ]);
    expect($query)->not->toHaveKey('database');
    expect($query)->not->toHaveKey('display_limit');
    expect($query)->not->toHaveKey('display_offset');
});

it('builds batch comparison query correctly', function (): void {
    $request = new BatchComparisonRequest(
        targets: ['example.com', 'example.org'],
        targetTypes: ['root_domain', 'root_domain'],
        exportColumns: 'target,ascore,total',
        displayLimit: 20,
        displayOffset: 5,
    );

    $reflection = new ReflectionClass($request);
    $method = $reflection->getMethod('defaultQuery');

    $query = $method->invoke($request);

    expect($request->resolveEndpoint())->toBe('/analytics/v1/');
    expect($query)->toMatchArray([
        'type' => 'backlinks_comparison',
        'targets' => ['example.com', 'example.org'],
        'target_types' => ['root_domain', 'root_domain'],
        'export_columns' => 'target,ascore,total',
        'key' => 'test-semrush-key',
    ]);
    expect($query)->not->toHaveKey('database');
    expect($query)->not->toHaveKey('display_limit');
    expect($query)->not->toHaveKey('display_offset');
});

it('serializes batch comparison targets with bracket notation (no indexes)', function (): void {
    $mockClient = new MockClient([
        BatchComparisonRequest::class => MockResponse::make("target;metric\nexample.com;10\nexample.org;20", 200),
    ]);

    $connector = SemrushConnector::forScope('semrush_connector_test');
    $connector->withMockClient($mockClient);

    $response = $connector->send(new BatchComparisonRequest(
        targets: ['example.com', 'example.org'],
        targetTypes: ['root_domain', 'root_domain'],
        exportColumns: 'target,ascore,total',
    ));

    $query = $response->getPsrRequest()->getUri()->getQuery();

    expect($query)->toContain('targets%5B%5D=example.com');
    expect($query)->toContain('targets%5B%5D=example.org');
    expect($query)->toContain('target_types%5B%5D=root_domain');
    expect($query)->not->toContain('targets%5B0%5D=');
    expect($query)->not->toContain('target_types%5B0%5D=');
});

it('rejects batch comparison when target counts mismatch', function (): void {
    expect(fn (): BatchComparisonRequest => new BatchComparisonRequest(
        targets: ['example.com', 'example.org'],
        targetTypes: ['root_domain'],
        exportColumns: 'domain,ascore,backlinks',
    ))->toThrow(InvalidArgumentException::class);
});

it('builds api units balance query correctly', function (): void {
    $request = new ApiUnitsBalanceRequest;

    $reflection = new ReflectionClass($request);
    $method = $reflection->getMethod('defaultQuery');

    $query = $method->invoke($request);

    expect($request->resolveEndpoint())->toBe('https://www.semrush.com/users/countapiunits.html');
    expect($query)->toMatchArray([
        'key' => 'test-semrush-key',
    ]);
    expect($query)->not->toHaveKey('type');
});

it('maps backlinks overview csv response into dto', function (): void {
    $connector = SemrushConnector::forScope('semrush_connector_test');
    $connector->withMockClient(new MockClient([
        BacklinksOverviewRequest::class => MockResponse::make("domain;ascore;backlinks\nexample.com;12;100", 200),
    ]));

    $response = $connector->send(new BacklinksOverviewRequest(
        target: 'example.com',
        targetType: 'root_domain',
        exportColumns: 'domain,ascore,backlinks',
    ));

    $dto = $response->dtoOrFail();

    expect($dto)->toBeInstanceOf(BacklinksOverviewResponseData::class);
    expect($dto->headers)->toBe(['domain', 'ascore', 'backlinks']);
    expect($dto->rows)->toBe([
        [
            'domain' => 'example.com',
            'ascore' => '12',
            'backlinks' => '100',
        ],
    ]);
    expect($dto->rowCount)->toBe(1);
});

it('maps batch comparison csv response into dto', function (): void {
    $connector = SemrushConnector::forScope('semrush_connector_test');
    $connector->withMockClient(new MockClient([
        BatchComparisonRequest::class => MockResponse::make("target;metric\nexample.com;10\nexample.org;20", 200),
    ]));

    $response = $connector->send(new BatchComparisonRequest(
        targets: ['example.com', 'example.org'],
        targetTypes: ['root_domain', 'root_domain'],
        exportColumns: 'target,metric',
    ));

    $dto = $response->dtoOrFail();

    expect($dto)->toBeInstanceOf(BatchComparisonResponseData::class);
    expect($dto->headers)->toBe(['target', 'metric']);
    expect($dto->rows)->toBe([
        ['target' => 'example.com', 'metric' => '10'],
        ['target' => 'example.org', 'metric' => '20'],
    ]);
    expect($dto->rowCount)->toBe(2);
});

it('auto-detects comma delimiter for semrush csv parsing', function (): void {
    $connector = SemrushConnector::forScope('semrush_connector_test');
    $connector->withMockClient(new MockClient([
        BacklinksOverviewRequest::class => MockResponse::make("domain,ascore,backlinks\nexample.com,12,100", 200),
    ]));

    $response = $connector->send(new BacklinksOverviewRequest(
        target: 'example.com',
        targetType: 'root_domain',
        exportColumns: 'domain,ascore,backlinks',
    ));

    $dto = $response->dtoOrFail();

    expect($dto->headers)->toBe(['domain', 'ascore', 'backlinks']);
    expect($dto->rows[0]['domain'])->toBe('example.com');
    expect($dto->rows[0]['ascore'])->toBe('12');
    expect($dto->rows[0]['backlinks'])->toBe('100');
});

it('returns an empty dto for empty semrush csv body', function (): void {
    $connector = SemrushConnector::forScope('semrush_connector_test');
    $connector->withMockClient(new MockClient([
        BacklinksOverviewRequest::class => MockResponse::make('', 200),
    ]));

    $response = $connector->send(new BacklinksOverviewRequest(
        target: 'example.com',
        targetType: 'root_domain',
        exportColumns: 'domain,ascore,backlinks',
    ));

    $dto = $response->dtoOrFail();

    expect($dto->headers)->toBe([]);
    expect($dto->rows)->toBe([]);
    expect($dto->rowCount)->toBe(0);
});

it('throws when semrush csv row has mismatched column count', function (): void {
    $connector = SemrushConnector::forScope('semrush_connector_test');
    $connector->withMockClient(new MockClient([
        BacklinksOverviewRequest::class => MockResponse::make("domain;ascore;backlinks\nexample.com;12", 200),
    ]));

    $response = $connector->send(new BacklinksOverviewRequest(
        target: 'example.com',
        targetType: 'root_domain',
        exportColumns: 'domain,ascore,backlinks',
    ));

    expect(fn (): mixed => $response->dtoOrFail())->toThrow(RuntimeException::class);
});

it('throws when semrush csv headers contain duplicates', function (): void {
    $connector = SemrushConnector::forScope('semrush_connector_test');
    $connector->withMockClient(new MockClient([
        BacklinksOverviewRequest::class => MockResponse::make("domain;domain\nexample.com;example.org", 200),
    ]));

    $response = $connector->send(new BacklinksOverviewRequest(
        target: 'example.com',
        targetType: 'root_domain',
        exportColumns: 'domain,ascore,backlinks',
    ));

    expect(fn (): mixed => $response->dtoOrFail())->toThrow(RuntimeException::class);
});

it('maps semrush api units balance response into dto', function (): void {
    $connector = SemrushConnector::forScope('semrush_connector_test');
    $connector->withMockClient(new MockClient([
        ApiUnitsBalanceRequest::class => MockResponse::make('123456', 200),
    ]));

    $response = $connector->send(new ApiUnitsBalanceRequest);
    $dto = $response->dtoOrFail();

    expect($dto)->toBeInstanceOf(ApiUnitsBalanceResponseData::class);
    expect($dto->units)->toBe(123456);
});

it('maps comma-separated semrush api units balance response into dto', function (): void {
    $connector = SemrushConnector::forScope('semrush_connector_test');
    $connector->withMockClient(new MockClient([
        ApiUnitsBalanceRequest::class => MockResponse::make('1,999,800', 200),
    ]));

    $response = $connector->send(new ApiUnitsBalanceRequest);
    $dto = $response->dtoOrFail();

    expect($dto)->toBeInstanceOf(ApiUnitsBalanceResponseData::class);
    expect($dto->units)->toBe(1999800);
});

it('throws when semrush api units balance response is invalid', function (): void {
    $connector = SemrushConnector::forScope('semrush_connector_test');
    $connector->withMockClient(new MockClient([
        ApiUnitsBalanceRequest::class => MockResponse::make('not-a-number', 200),
    ]));

    $response = $connector->send(new ApiUnitsBalanceRequest);

    expect(fn (): mixed => $response->dtoOrFail())->toThrow(RuntimeException::class);
});
