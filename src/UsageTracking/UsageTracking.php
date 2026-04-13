<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking;

use Seeders\ExternalApis\UsageTracking\Models\AiUsageLog;
use Seeders\ExternalApis\UsageTracking\Models\ApiConsumptionLog;

class UsageTracking
{
    /** @var class-string */
    public static string $apiUsageLogModel = AiUsageLog::class;

    /** @var class-string */
    public static string $apiLogModel = ApiConsumptionLog::class;

    public static string $userModel = 'App\\Models\\User';

    public static function useApiUsageLogModel(string $model): void
    {
        static::$apiUsageLogModel = $model;
    }

    public static function useApiLogModel(string $model): void
    {
        static::$apiLogModel = $model;
    }

    public static function useUserModel(string $model): void
    {
        static::$userModel = $model;
    }
}
