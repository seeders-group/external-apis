<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Services;

use Illuminate\Support\Facades\Log;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

class DataForSeoUsageTrackerService
{
    /**
     * Log a DataForSEO API request.
     * DataForSEO returns cost in USD directly in the response.
     */
    public function logRequest(
        string $endpoint,
        float $cost,
        array $context = []
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        $log = $logModel::create([
            'integration' => 'dataforseo',
            'model' => null,
            'endpoint' => $endpoint,
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
            'total_tokens' => 1,
            'estimated_cost' => $cost,
            'actual_cost' => $cost,
            'feature' => $context['feature'] ?? $this->extractFeatureFromEndpoint($endpoint),
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'success',
            'metadata' => array_merge($context['metadata'] ?? [], [
                'cost_usd' => $cost,
            ]),
            'reconciled_at' => now(),
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
            app(BudgetAlertService::class)->checkAndAlert('dataforseo');
        } catch (\Throwable $e) {
            Log::warning('Failed to check DataForSEO budget threshold: '.$e->getMessage());
        }
    }

    /**
     * Log a DataForSEO API error.
     */
    public function logError(
        string $endpoint,
        string $errorMessage,
        array $context = []
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => 'dataforseo',
            'model' => null,
            'endpoint' => $endpoint,
            'estimated_cost' => 0,
            'feature' => $context['feature'] ?? $this->extractFeatureFromEndpoint($endpoint),
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'error',
            'error_message' => $errorMessage,
            'metadata' => $context['metadata'] ?? null,
        ]);
    }

    /**
     * Extract feature name from endpoint.
     */
    private function extractFeatureFromEndpoint(string $endpoint): string
    {
        $parts = explode('/', trim($endpoint, '/'));

        $index = ($parts[0] ?? '') === 'v3' ? 1 : 0;

        $apiType = $parts[$index] ?? 'unknown';

        return match ($apiType) {
            'serp' => 'serp',
            'business_data' => 'reviews',
            'on_page' => 'on_page',
            'keywords_data' => 'keywords',
            'backlinks' => 'backlinks',
            'domain_analytics' => 'domain_analytics',
            'content_analysis' => 'content_analysis',
            'content_generation' => 'content_generation',
            'merchant' => 'merchant',
            'app_data' => 'app_data',
            default => $apiType,
        };
    }

    /**
     * Get today's DataForSEO spend.
     */
    public function getTodaySpend(): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (float) $logModel::byIntegration('dataforseo')
            ->today()
            ->sum('actual_cost');
    }

    /**
     * Get today's request count.
     */
    public function getTodayRequests(): int
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (int) $logModel::byIntegration('dataforseo')
            ->today()
            ->count();
    }

    /**
     * Get month-to-date spend.
     */
    public function getMonthToDateSpend(): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (float) $logModel::byIntegration('dataforseo')
            ->thisMonth()
            ->sum('actual_cost');
    }

    /**
     * Get month-to-date request count.
     */
    public function getMonthToDateRequests(): int
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (int) $logModel::byIntegration('dataforseo')
            ->thisMonth()
            ->count();
    }

    /**
     * Get spend by feature for current month.
     */
    public function getMonthSpendByFeature(): array
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::byIntegration('dataforseo')
            ->thisMonth()
            ->selectRaw('feature, SUM(actual_cost) as total_cost, COUNT(*) as request_count')
            ->groupBy('feature')
            ->get()
            ->toArray();
    }
}
