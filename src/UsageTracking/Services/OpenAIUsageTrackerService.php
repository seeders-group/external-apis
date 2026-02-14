<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Services;

use Seeders\ExternalApis\UsageTracking\UsageTracking;

class OpenAIUsageTrackerService
{
    public function logRequest(
        string $model,
        int $promptTokens,
        int $completionTokens,
        array $context,
        int $cachedTokens = 0,
        ?string $requestId = null,
        string $endpoint = 'chat.completions'
    ) {
        $cost = $this->calculateCost($model, $promptTokens, $completionTokens, $cachedTokens);

        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => 'openai',
            'request_id' => $requestId,
            'model' => $model,
            'endpoint' => $endpoint,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $promptTokens + $completionTokens,
            'input_cached_tokens' => $cachedTokens,
            'estimated_cost' => $cost,
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'success',
            'metadata' => $context['metadata'] ?? null,
        ]);
    }

    public function logImageGeneration(
        string $model,
        int $imagesGenerated,
        string $size,
        string $quality,
        array $context
    ) {
        $cost = $this->calculateImageCost($model, $size, $quality, $imagesGenerated);

        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => 'openai',
            'model' => $model,
            'endpoint' => 'images.generations',
            'images_generated' => $imagesGenerated,
            'estimated_cost' => $cost,
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'success',
            'metadata' => array_merge($context['metadata'] ?? [], [
                'size' => $size,
                'quality' => $quality,
            ]),
        ]);
    }

    public function logError(
        string $model,
        string $errorMessage,
        array $context
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => 'openai',
            'model' => $model,
            'estimated_cost' => 0,
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'error',
            'error_message' => $errorMessage,
            'metadata' => $context['metadata'] ?? null,
        ]);
    }

    public function calculateCost(
        string $model,
        int $promptTokens,
        int $completionTokens,
        int $cachedTokens = 0
    ): float {
        $pricingModel = UsageTracking::$aiModelPricingModel;
        $pricing = $pricingModel::getPricing($model, 'openai');

        if (! $pricing) {
            return 0.0;
        }

        $regularInputTokens = max(0, $promptTokens - $cachedTokens);
        $inputCost = ($regularInputTokens / 1_000_000) * $pricing['input_per_1m_tokens'];

        $cachedCost = 0;
        if ($cachedTokens > 0 && isset($pricing['cached_input_per_1m_tokens']) && $pricing['cached_input_per_1m_tokens']) {
            $cachedCost = ($cachedTokens / 1_000_000) * $pricing['cached_input_per_1m_tokens'];
        }

        $outputCost = ($completionTokens / 1_000_000) * $pricing['output_per_1m_tokens'];

        return round($inputCost + $cachedCost + $outputCost, 6);
    }

    public function calculateImageCost(
        string $model,
        string $size,
        string $quality,
        int $count = 1
    ): float {
        $pricing = config("external-apis.usage_tracking.pricing.openai.models.{$model}", config("ai_pricing.openai.models.{$model}"));

        if (! $pricing) {
            return 0.0;
        }

        $key = "{$quality}_{$size}";
        $pricePerImage = $pricing[$key] ?? $pricing[$size] ?? 0;

        return round($pricePerImage * $count, 6);
    }

    public function getTodaySpend(): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::byIntegration('openai')
            ->today()
            ->sum('estimated_cost');
    }

    public function getMonthToDateSpend(): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::byIntegration('openai')
            ->thisMonth()
            ->sum('actual_cost') ?: $logModel::byIntegration('openai')
            ->thisMonth()
            ->sum('estimated_cost');
    }

    public function checkBudgetThreshold(): array
    {
        $budgetModel = UsageTracking::$apiBudgetConfigModel;
        $budget = $budgetModel::getOpenAIBudget();

        if (! $budget) {
            return [
                'status' => 'no_budget',
                'percentage' => 0,
                'message' => 'No budget configured',
            ];
        }

        if (empty($budget->monthly_budget) || (float) $budget->monthly_budget <= 0.0) {
            return [
                'status' => 'invalid_budget',
                'percentage' => 0,
                'message' => 'Monthly budget must be greater than zero',
            ];
        }

        $monthToDateSpend = $this->getMonthToDateSpend();
        $percentage = ($monthToDateSpend / $budget->monthly_budget) * 100;

        if ($percentage >= $budget->critical_threshold) {
            return [
                'status' => 'critical',
                'percentage' => round($percentage, 2),
                'spent' => $monthToDateSpend,
                'budget' => $budget->monthly_budget,
                'remaining' => $budget->monthly_budget - $monthToDateSpend,
                'message' => 'Critical: Budget threshold exceeded',
            ];
        }

        if ($percentage >= $budget->warning_threshold) {
            return [
                'status' => 'warning',
                'percentage' => round($percentage, 2),
                'spent' => $monthToDateSpend,
                'budget' => $budget->monthly_budget,
                'remaining' => $budget->monthly_budget - $monthToDateSpend,
                'message' => 'Warning: Approaching budget limit',
            ];
        }

        return [
            'status' => 'ok',
            'percentage' => round($percentage, 2),
            'spent' => $monthToDateSpend,
            'budget' => $budget->monthly_budget,
            'remaining' => $budget->monthly_budget - $monthToDateSpend,
            'message' => 'Within budget',
        ];
    }
}
