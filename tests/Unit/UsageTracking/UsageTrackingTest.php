<?php

declare(strict_types=1);

use Seeders\ExternalApis\UsageTracking\Models\AiUsageLog;
use Seeders\ExternalApis\UsageTracking\Models\ApiConsumptionLog;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

it('has correct default models', function (): void {
    expect(UsageTracking::$apiUsageLogModel)->toBe(AiUsageLog::class);
    expect(UsageTracking::$apiLogModel)->toBe(ApiConsumptionLog::class);
});

it('can swap the api usage log model', function (): void {
    $original = UsageTracking::$apiUsageLogModel;

    UsageTracking::useApiUsageLogModel('App\\Models\\CustomApiUsageLog');
    expect(UsageTracking::$apiUsageLogModel)->toBe('App\\Models\\CustomApiUsageLog');

    UsageTracking::useApiUsageLogModel($original);
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
