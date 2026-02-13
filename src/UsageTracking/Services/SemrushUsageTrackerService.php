<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Services;

use Throwable;
use Illuminate\Support\Facades\Log;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

class SemrushUsageTrackerService
{
    public function logRequest(
        string $endpoint,
        string $requestType,
        int $unitsConsumed,
        array $context = []
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        $log = $logModel::create([
            'integration' => 'semrush',
            'model' => null,
            'endpoint' => $endpoint,
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
            'total_tokens' => $unitsConsumed,
            'estimated_cost' => $this->calculateCost($unitsConsumed, $requestType),
            'feature' => $context['feature'] ?? 'semrush',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'success',
            'metadata' => array_merge($context['metadata'] ?? [], [
                'request_type' => $requestType,
                'units_consumed' => $unitsConsumed,
                'target_count' => $context['target_count'] ?? null,
            ]),
        ]);

        $this->checkBudgetThreshold();

        return $log;
    }

    public function logError(
        string $endpoint,
        string $requestType,
        string $errorMessage,
        array $context = []
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => 'semrush',
            'model' => null,
            'endpoint' => $endpoint,
            'total_tokens' => 0,
            'estimated_cost' => 0,
            'feature' => $context['feature'] ?? 'semrush',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'error',
            'error_message' => $errorMessage,
            'metadata' => array_merge($context['metadata'] ?? [], [
                'request_type' => $requestType,
                'units_consumed' => 0,
                'target_count' => $context['target_count'] ?? null,
            ]),
        ]);
    }

    public function calculateCost(int $unitsConsumed, ?string $requestType = null): float
    {
        $pricingModel = UsageTracking::$apiServicePricingModel;

        return $pricingModel::calculateCost('semrush', $unitsConsumed, $requestType);
    }

    public function getTodaySpend(): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (float) $logModel::byIntegration('semrush')
            ->today()
            ->sum('estimated_cost');
    }

    public function getTodayUnitsConsumed(): int
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (int) $logModel::byIntegration('semrush')
            ->today()
            ->sum('total_tokens');
    }

    public function getMonthToDateSpend(): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (float) $logModel::byIntegration('semrush')
            ->thisMonth()
            ->sum('estimated_cost');
    }

    public function getMonthToDateUnitsConsumed(): int
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (int) $logModel::byIntegration('semrush')
            ->thisMonth()
            ->sum('total_tokens');
    }

    private function checkBudgetThreshold(): void
    {
        try {
            resolve(BudgetAlertService::class)->checkAndAlert('semrush');
        } catch (Throwable $e) {
            Log::warning('Failed to check Semrush budget threshold: '.$e->getMessage());
        }
    }
}
