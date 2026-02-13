<?php

declare(strict_types=1);

use Seeders\ExternalApis\UsageTracking\Models\AiModelPricing;
use Seeders\ExternalApis\UsageTracking\Models\ApiBudgetConfig;
use Seeders\ExternalApis\UsageTracking\Models\ApiLog;
use Seeders\ExternalApis\UsageTracking\Models\ApiServicePricing;
use Seeders\ExternalApis\UsageTracking\Models\ApiUsageLog;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

it('has correct default models', function (): void {
    expect(UsageTracking::$apiUsageLogModel)->toBe(ApiUsageLog::class);
    expect(UsageTracking::$aiModelPricingModel)->toBe(AiModelPricing::class);
    expect(UsageTracking::$apiServicePricingModel)->toBe(ApiServicePricing::class);
    expect(UsageTracking::$apiBudgetConfigModel)->toBe(ApiBudgetConfig::class);
    expect(UsageTracking::$apiLogModel)->toBe(ApiLog::class);
});

it('can swap the api usage log model', function (): void {
    $original = UsageTracking::$apiUsageLogModel;

    UsageTracking::useApiUsageLogModel('App\\Models\\CustomApiUsageLog');
    expect(UsageTracking::$apiUsageLogModel)->toBe('App\\Models\\CustomApiUsageLog');

    UsageTracking::useApiUsageLogModel($original);
});

it('can swap the ai model pricing model', function (): void {
    $original = UsageTracking::$aiModelPricingModel;

    UsageTracking::useAiModelPricingModel('App\\Models\\CustomPricing');
    expect(UsageTracking::$aiModelPricingModel)->toBe('App\\Models\\CustomPricing');

    UsageTracking::useAiModelPricingModel($original);
});

it('can swap the api service pricing model', function (): void {
    $original = UsageTracking::$apiServicePricingModel;

    UsageTracking::useApiServicePricingModel('App\\Models\\CustomServicePricing');
    expect(UsageTracking::$apiServicePricingModel)->toBe('App\\Models\\CustomServicePricing');

    UsageTracking::useApiServicePricingModel($original);
});

it('can swap the api budget config model', function (): void {
    $original = UsageTracking::$apiBudgetConfigModel;

    UsageTracking::useApiBudgetConfigModel('App\\Models\\CustomBudgetConfig');
    expect(UsageTracking::$apiBudgetConfigModel)->toBe('App\\Models\\CustomBudgetConfig');

    UsageTracking::useApiBudgetConfigModel($original);
});

it('can swap the api log model', function (): void {
    $original = UsageTracking::$apiLogModel;

    UsageTracking::useApiLogModel('App\\Models\\CustomApiLog');
    expect(UsageTracking::$apiLogModel)->toBe('App\\Models\\CustomApiLog');

    UsageTracking::useApiLogModel($original);
});

it('can swap the user model', function (): void {
    $original = UsageTracking::$userModel;

    UsageTracking::useUserModel('App\\Models\\CustomUser');
    expect(UsageTracking::$userModel)->toBe('App\\Models\\CustomUser');

    UsageTracking::useUserModel($original);
});
