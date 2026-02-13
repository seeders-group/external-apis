<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking;

use Seeders\ExternalApis\UsageTracking\Models\AiModelPricing;
use Seeders\ExternalApis\UsageTracking\Models\ApiBudgetConfig;
use Seeders\ExternalApis\UsageTracking\Models\ApiLog;
use Seeders\ExternalApis\UsageTracking\Models\ApiServicePricing;
use Seeders\ExternalApis\UsageTracking\Models\ApiUsageLog;

class UsageTracking
{
    /** @var class-string */
    public static string $apiUsageLogModel = ApiUsageLog::class;

    /** @var class-string */
    public static string $aiModelPricingModel = AiModelPricing::class;

    /** @var class-string */
    public static string $apiServicePricingModel = ApiServicePricing::class;

    /** @var class-string */
    public static string $apiBudgetConfigModel = ApiBudgetConfig::class;

    /** @var class-string */
    public static string $apiLogModel = ApiLog::class;

    /** @var string */
    public static string $userModel = 'App\\Models\\User';

    public static function useApiUsageLogModel(string $model): void
    {
        static::$apiUsageLogModel = $model;
    }

    public static function useAiModelPricingModel(string $model): void
    {
        static::$aiModelPricingModel = $model;
    }

    public static function useApiServicePricingModel(string $model): void
    {
        static::$apiServicePricingModel = $model;
    }

    public static function useApiBudgetConfigModel(string $model): void
    {
        static::$apiBudgetConfigModel = $model;
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
