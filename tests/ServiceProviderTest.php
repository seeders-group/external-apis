<?php

declare(strict_types=1);

use Seeders\ExternalApis\Clients\GeminiClient;
use Seeders\ExternalApis\Clients\OpenAIClient;
use Seeders\ExternalApis\Connectors\Ahrefs\AhrefsConnector;
use Seeders\ExternalApis\Connectors\DataForSeo\DataForSeoConnector;
use Seeders\ExternalApis\Connectors\Hunter\HunterConnector;
use Seeders\ExternalApis\Connectors\Moz\MozLinksConnector;
use Seeders\ExternalApis\Connectors\Semrush\SemrushConnector;
use Seeders\ExternalApis\UsageTracking\Services\SemrushUsageTrackerService;

it('registers the ahrefs connector', function () {
    $connector = app(AhrefsConnector::class);

    expect($connector)->toBeInstanceOf(AhrefsConnector::class);
});

it('registers the dataforseo connector', function () {
    $connector = app(DataForSeoConnector::class);

    expect($connector)->toBeInstanceOf(DataForSeoConnector::class);
});

it('registers the hunter connector', function () {
    $connector = app(HunterConnector::class);

    expect($connector)->toBeInstanceOf(HunterConnector::class);
});

it('registers the moz connector', function () {
    $connector = app(MozLinksConnector::class);

    expect($connector)->toBeInstanceOf(MozLinksConnector::class);
});

it('registers the semrush connector', function () {
    $connector = app(SemrushConnector::class);

    expect($connector)->toBeInstanceOf(SemrushConnector::class);
});

it('registers semrush connector as transient', function () {
    $firstConnector = app(SemrushConnector::class);
    $secondConnector = app(SemrushConnector::class);

    expect($firstConnector)->not->toBe($secondConnector);
});

it('registers the semrush usage tracker service', function () {
    $service = app(SemrushUsageTrackerService::class);

    expect($service)->toBeInstanceOf(SemrushUsageTrackerService::class);
});

it('registers the openai client', function () {
    $client = app(OpenAIClient::class);

    expect($client)->toBeInstanceOf(OpenAIClient::class);
});

it('registers the gemini client', function () {
    $client = app(GeminiClient::class);

    expect($client)->toBeInstanceOf(GeminiClient::class);
});

it('loads the configuration', function () {
    expect(config('external-apis.ahrefs.token'))->toBe('test-token');
    expect(config('external-apis.dataforseo.username'))->toBe('test-user');
    expect(config('external-apis.openai.key'))->toBe('test-key');
    expect(config('external-apis.semrush.api_key'))->toBe('test-semrush-key');
    expect(config('external-apis.usage_tracking.pricing.semrush.cost_per_unit'))->toBe(0.00005);
});
