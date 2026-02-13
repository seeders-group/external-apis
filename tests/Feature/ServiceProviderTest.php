<?php

declare(strict_types=1);

use Seeders\ExternalApis\Integrations\Ahrefs\AhrefsConnector;
use Seeders\ExternalApis\Integrations\DataForSeo\DataForSeoConnector;
use Seeders\ExternalApis\Integrations\Hunter\HunterConnector;
use Seeders\ExternalApis\Integrations\Moz\MozLinksConnector;
use Seeders\ExternalApis\Integrations\Semrush\SemrushConnector;
use Seeders\ExternalApis\UsageTracking\Services\SemrushUsageTrackerService;

it('registers the ahrefs connector', function (): void {
    $connector = resolve(AhrefsConnector::class);

    expect($connector)->toBeInstanceOf(AhrefsConnector::class);
});

it('registers the dataforseo connector', function (): void {
    $connector = resolve(DataForSeoConnector::class);

    expect($connector)->toBeInstanceOf(DataForSeoConnector::class);
});

it('registers the hunter connector', function (): void {
    $connector = resolve(HunterConnector::class);

    expect($connector)->toBeInstanceOf(HunterConnector::class);
});

it('registers the moz connector', function (): void {
    $connector = resolve(MozLinksConnector::class);

    expect($connector)->toBeInstanceOf(MozLinksConnector::class);
});

it('registers the semrush connector', function (): void {
    $connector = resolve(SemrushConnector::class);

    expect($connector)->toBeInstanceOf(SemrushConnector::class);
});

it('registers semrush connector as transient', function (): void {
    $firstConnector = resolve(SemrushConnector::class);
    $secondConnector = resolve(SemrushConnector::class);

    expect($firstConnector)->not->toBe($secondConnector);
});

it('registers the semrush usage tracker service', function (): void {
    $service = resolve(SemrushUsageTrackerService::class);

    expect($service)->toBeInstanceOf(SemrushUsageTrackerService::class);
});

it('loads the configuration', function (): void {
    expect(config('external-apis.ahrefs.token'))->toBe('test-token');
    expect(config('external-apis.dataforseo.username'))->toBe('test-user');
    expect(config('external-apis.openai.key'))->toBe('test-key');
    expect(config('external-apis.semrush.api_key'))->toBe('test-semrush-key');
    expect(config('external-apis.usage_tracking.pricing.semrush.cost_per_unit'))->toBe(0.00005);
});
