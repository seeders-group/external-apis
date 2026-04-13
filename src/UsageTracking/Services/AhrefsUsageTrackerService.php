<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Services;

use Seeders\ExternalApis\UsageTracking\UsageTracking;

class AhrefsUsageTrackerService
{
    /**
     * Log an Ahrefs API request.
     */
    public function logRequest(
        string $endpoint,
        int $unitsConsumed,
        array $context = []
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => 'ahrefs',
            'model' => null,
            'endpoint' => $endpoint,
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
            'total_tokens' => $unitsConsumed,
            'feature' => $context['feature'] ?? 'domain_analysis',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'success',
            'metadata' => array_merge($context['metadata'] ?? [], [
                'units_consumed' => $unitsConsumed,
            ]),
        ]);
    }

    /**
     * Log an Ahrefs API error.
     */
    public function logError(
        string $endpoint,
        string $errorMessage,
        array $context = []
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => 'ahrefs',
            'model' => null,
            'endpoint' => $endpoint,
            'feature' => $context['feature'] ?? 'domain_analysis',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'error',
            'error_message' => $errorMessage,
            'metadata' => $context['metadata'] ?? null,
        ]);
    }

    /**
     * Get today's units consumed.
     */
    public function getTodayUnitsConsumed(): int
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (int) $logModel::byIntegration('ahrefs')
            ->today()
            ->sum('total_tokens');
    }

    /**
     * Get month-to-date units consumed.
     */
    public function getMonthToDateUnitsConsumed(): int
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (int) $logModel::byIntegration('ahrefs')
            ->thisMonth()
            ->sum('total_tokens');
    }
}
