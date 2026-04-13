<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking;

use Seeders\ExternalApis\UsageTracking\Models\ApiLog;
use Seeders\ExternalApis\UsageTracking\Models\ApiUsageLog;

class UsageTracking
{
    /** @var class-string */
    public static string $apiUsageLogModel = ApiUsageLog::class;

    /** @var class-string */
    public static string $apiLogModel = ApiLog::class;

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
