<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Services;

use Illuminate\Support\Facades\Log;
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
        $cost = $this->calculateCost($unitsConsumed, $endpoint);

        $logModel = UsageTracking::$apiUsageLogModel;

        $log = $logModel::create([
            'integration' => 'ahrefs',
            'model' => null,
            'endpoint' => $endpoint,
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
            'total_tokens' => $unitsConsumed,
            'estimated_cost' => $cost,
            'feature' => $context['feature'] ?? 'domain_analysis',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'success',
            'metadata' => array_merge($context['metadata'] ?? [], [
                'units_consumed' => $unitsConsumed,
            ]),
        ]);

        $this->checkBudgetThreshold();

        return $log;
    }

    /**
     * Check if budget threshold has been reached and send alert.
     */
    private function checkBudgetThreshold(): void
    {
        try {
            app(BudgetAlertService::class)->checkAndAlert('ahrefs');
        } catch (\Throwable $e) {
            Log::warning('Failed to check Ahrefs budget threshold: '.$e->getMessage());
        }
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
            'estimated_cost' => 0,
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
     * Calculate cost from units consumed.
     */
    public function calculateCost(int $unitsConsumed, ?string $endpoint = null): float
    {
        $pricingModel = UsageTracking::$apiServicePricingModel;

        return $pricingModel::calculateCost('ahrefs', $unitsConsumed, $endpoint);
    }

    /**
     * Get today's Ahrefs spend.
     */
    public function getTodaySpend(): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (float) $logModel::byIntegration('ahrefs')
            ->today()
            ->sum('estimated_cost');
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
     * Get month-to-date spend.
     */
    public function getMonthToDateSpend(): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (float) $logModel::byIntegration('ahrefs')
            ->thisMonth()
            ->sum('estimated_cost');
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
