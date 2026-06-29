<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Services;

use Seeders\ExternalApis\UsageTracking\UsageTracking;

class DataForSeoUsageTrackerService
{
    /**
     * Log a DataForSEO API request.
     */
    public function logRequest(
        string $endpoint,
        array $context = []
    ): mixed {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => 'dataforseo',
            'model' => null,
            'endpoint' => $endpoint,
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
            'total_tokens' => 1,
            'feature' => $context['feature'] ?? $this->extractFeatureFromEndpoint($endpoint),
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'success',
            'metadata' => $context['metadata'] ?? null,
        ]);
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

        $index = $parts[0] === 'v3' ? 1 : 0;

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
     * Get month-to-date request count.
     */
    public function getMonthToDateRequests(): int
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return (int) $logModel::byIntegration('dataforseo')
            ->thisMonth()
            ->count();
    }
}
