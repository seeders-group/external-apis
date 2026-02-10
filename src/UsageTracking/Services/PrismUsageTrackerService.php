<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Services;

use Prism\Prism\Enums\Provider;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

class PrismUsageTrackerService
{
    /**
     * Log a Prism text/structured generation request.
     */
    public function logRequest(
        Provider $provider,
        string $model,
        int $promptTokens,
        int $completionTokens,
        array $context,
        int $cachedTokens = 0,
        ?int $thoughtTokens = null,
        ?string $requestId = null,
        string $endpoint = 'text'
    ) {
        $integration = $this->providerToIntegration($provider);
        $cost = $this->calculateCost($integration, $model, $promptTokens, $completionTokens, $cachedTokens);

        $metadata = $context['metadata'] ?? [];
        if ($thoughtTokens !== null && $thoughtTokens > 0) {
            $metadata['thought_tokens'] = $thoughtTokens;
        }

        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $integration,
            'request_id' => $requestId,
            'model' => $model,
            'endpoint' => "prism.{$endpoint}",
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
            'metadata' => ! empty($metadata) ? $metadata : null,
        ]);
    }

    /**
     * Log a Prism embeddings request.
     */
    public function logEmbeddingsRequest(
        Provider $provider,
        string $model,
        int $tokensUsed,
        array $context,
        ?string $requestId = null
    ) {
        $integration = $this->providerToIntegration($provider);
        $cost = $this->calculateEmbeddingsCost($integration, $model, $tokensUsed);

        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $integration,
            'request_id' => $requestId,
            'model' => $model,
            'endpoint' => 'prism.embeddings',
            'prompt_tokens' => $tokensUsed,
            'completion_tokens' => 0,
            'total_tokens' => $tokensUsed,
            'estimated_cost' => $cost,
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'success',
            'metadata' => $context['metadata'] ?? null,
        ]);
    }

    /**
     * Log a Prism image generation request.
     */
    public function logImageGeneration(
        Provider $provider,
        string $model,
        int $imagesGenerated,
        array $context,
        ?string $size = null,
        ?string $quality = null
    ) {
        $integration = $this->providerToIntegration($provider);
        $cost = $this->calculateImageCost($integration, $model, $size, $quality, $imagesGenerated);

        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $integration,
            'model' => $model,
            'endpoint' => 'prism.images',
            'images_generated' => $imagesGenerated,
            'estimated_cost' => $cost,
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'success',
            'metadata' => array_filter([
                'size' => $size,
                'quality' => $quality,
                ...$context['metadata'] ?? [],
            ]),
        ]);
    }

    /**
     * Log a Prism audio request (TTS or STT).
     */
    public function logAudioRequest(
        Provider $provider,
        string $model,
        array $context,
        ?int $charactersProcessed = null,
        ?int $secondsProcessed = null,
        ?string $requestId = null
    ) {
        $integration = $this->providerToIntegration($provider);
        $cost = $this->calculateAudioCost($integration, $model, $charactersProcessed, $secondsProcessed);

        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $integration,
            'request_id' => $requestId,
            'model' => $model,
            'endpoint' => 'prism.audio',
            'characters_processed' => $charactersProcessed,
            'seconds_processed' => $secondsProcessed,
            'estimated_cost' => $cost,
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'success',
            'metadata' => $context['metadata'] ?? null,
        ]);
    }

    /**
     * Log a Prism error.
     */
    public function logError(
        Provider $provider,
        string $model,
        string $errorMessage,
        array $context
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $this->providerToIntegration($provider),
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

    /**
     * Calculate cost for text/structured generation.
     */
    public function calculateCost(
        string $integration,
        string $model,
        int $promptTokens,
        int $completionTokens,
        int $cachedTokens = 0
    ): float {
        $pricingModel = UsageTracking::$aiModelPricingModel;
        $pricing = $pricingModel::getPricing($model, $integration);

        if (! $pricing) {
            return 0.0;
        }

        $regularInputTokens = $promptTokens - $cachedTokens;
        $inputCost = ($regularInputTokens / 1_000_000) * $pricing['input_per_1m_tokens'];

        $cachedCost = 0;
        if ($cachedTokens > 0 && isset($pricing['cached_input_per_1m_tokens']) && $pricing['cached_input_per_1m_tokens']) {
            $cachedCost = ($cachedTokens / 1_000_000) * $pricing['cached_input_per_1m_tokens'];
        }

        $outputCost = ($completionTokens / 1_000_000) * $pricing['output_per_1m_tokens'];

        return round($inputCost + $cachedCost + $outputCost, 6);
    }

    /**
     * Calculate cost for embeddings.
     */
    public function calculateEmbeddingsCost(string $integration, string $model, int $tokens): float
    {
        $pricingModel = UsageTracking::$aiModelPricingModel;
        $pricing = $pricingModel::getPricing($model, $integration);

        if (! $pricing) {
            $embeddingPrice = config("external-apis.usage_tracking.pricing.{$integration}.embeddings.{$model}")
                ?? config("ai_pricing.{$integration}.embeddings.{$model}");
            if ($embeddingPrice) {
                return round(($tokens / 1_000_000) * $embeddingPrice, 6);
            }

            return 0.0;
        }

        return round(($tokens / 1_000_000) * $pricing['input_per_1m_tokens'], 6);
    }

    /**
     * Calculate cost for image generation.
     */
    public function calculateImageCost(
        string $integration,
        string $model,
        ?string $size,
        ?string $quality,
        int $count = 1
    ): float {
        $pricing = config("external-apis.usage_tracking.pricing.{$integration}.models.{$model}")
            ?? config("ai_pricing.{$integration}.models.{$model}");

        if (! $pricing) {
            return 0.0;
        }

        if ($size && $quality) {
            $key = "{$quality}_{$size}";
            $pricePerImage = $pricing[$key] ?? $pricing[$size] ?? 0;
        } elseif ($size) {
            $pricePerImage = $pricing[$size] ?? 0;
        } else {
            $pricePerImage = $pricing['default'] ?? 0;
        }

        return round($pricePerImage * $count, 6);
    }

    /**
     * Calculate cost for audio processing.
     */
    public function calculateAudioCost(
        string $integration,
        string $model,
        ?int $characters,
        ?int $seconds
    ): float {
        $pricing = config("external-apis.usage_tracking.pricing.{$integration}.audio.{$model}")
            ?? config("ai_pricing.{$integration}.audio.{$model}");

        if (! $pricing) {
            return 0.0;
        }

        if ($characters && isset($pricing['per_1m_characters'])) {
            return round(($characters / 1_000_000) * $pricing['per_1m_characters'], 6);
        }

        if ($seconds && isset($pricing['per_minute'])) {
            return round(($seconds / 60) * $pricing['per_minute'], 6);
        }

        return 0.0;
    }

    /**
     * Convert Prism Provider enum to integration string.
     */
    public function providerToIntegration(Provider $provider): string
    {
        return $provider->value;
    }

    /**
     * Get today's spend for a specific provider.
     */
    public function getTodaySpend(Provider $provider): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::byIntegration($this->providerToIntegration($provider))
            ->today()
            ->sum('estimated_cost');
    }

    /**
     * Get month-to-date spend for a specific provider.
     */
    public function getMonthToDateSpend(Provider $provider): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;
        $integration = $this->providerToIntegration($provider);

        return $logModel::byIntegration($integration)
            ->thisMonth()
            ->sum('actual_cost') ?: $logModel::byIntegration($integration)
            ->thisMonth()
            ->sum('estimated_cost');
    }

    /**
     * Get total spend across all Prism providers.
     */
    public function getTotalPrismSpendToday(): float
    {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::whereIn('integration', $this->getAllPrismIntegrations())
            ->today()
            ->sum('estimated_cost');
    }

    /**
     * Get all Prism integration names.
     */
    protected function getAllPrismIntegrations(): array
    {
        return array_map(
            fn (Provider $provider) => $provider->value,
            Provider::cases()
        );
    }

    /**
     * Check budget threshold for a provider.
     */
    public function checkBudgetThreshold(Provider $provider): array
    {
        $integration = $this->providerToIntegration($provider);
        $budgetModel = UsageTracking::$apiBudgetConfigModel;
        $budget = $budgetModel::where('integration', $integration)->first();

        if (! $budget) {
            return [
                'status' => 'no_budget',
                'percentage' => 0,
                'message' => "No budget configured for {$integration}",
            ];
        }

        $monthToDateSpend = $this->getMonthToDateSpend($provider);
        $percentage = ($monthToDateSpend / $budget->monthly_budget) * 100;

        if ($percentage >= $budget->critical_threshold) {
            return [
                'status' => 'critical',
                'percentage' => round($percentage, 2),
                'spent' => $monthToDateSpend,
                'budget' => $budget->monthly_budget,
                'remaining' => $budget->monthly_budget - $monthToDateSpend,
                'message' => "Critical: {$integration} budget threshold exceeded",
            ];
        }

        if ($percentage >= $budget->warning_threshold) {
            return [
                'status' => 'warning',
                'percentage' => round($percentage, 2),
                'spent' => $monthToDateSpend,
                'budget' => $budget->monthly_budget,
                'remaining' => $budget->monthly_budget - $monthToDateSpend,
                'message' => "Warning: {$integration} approaching budget limit",
            ];
        }

        return [
            'status' => 'ok',
            'percentage' => round($percentage, 2),
            'spent' => $monthToDateSpend,
            'budget' => $budget->monthly_budget,
            'remaining' => $budget->monthly_budget - $monthToDateSpend,
            'message' => "{$integration} within budget",
        ];
    }
}
