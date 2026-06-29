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
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'trackable_type' => $context['trackable_type'] ?? null,
            'trackable_id' => $context['trackable_id'] ?? null,
            'status' => 'success',
            'metadata' => empty($metadata) ? null : $metadata,
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

        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $integration,
            'request_id' => $requestId,
            'model' => $model,
            'endpoint' => 'prism.embeddings',
            'prompt_tokens' => $tokensUsed,
            'completion_tokens' => 0,
            'total_tokens' => $tokensUsed,
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'trackable_type' => $context['trackable_type'] ?? null,
            'trackable_id' => $context['trackable_id'] ?? null,
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

        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $integration,
            'model' => $model,
            'endpoint' => 'prism.images',
            'images_generated' => $imagesGenerated,
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'trackable_type' => $context['trackable_type'] ?? null,
            'trackable_id' => $context['trackable_id'] ?? null,
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

        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $integration,
            'request_id' => $requestId,
            'model' => $model,
            'endpoint' => 'prism.audio',
            'characters_processed' => $charactersProcessed,
            'seconds_processed' => $secondsProcessed,
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'trackable_type' => $context['trackable_type'] ?? null,
            'trackable_id' => $context['trackable_id'] ?? null,
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
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'trackable_type' => $context['trackable_type'] ?? null,
            'trackable_id' => $context['trackable_id'] ?? null,
            'status' => 'error',
            'error_message' => $errorMessage,
            'metadata' => $context['metadata'] ?? null,
        ]);
    }

    /**
     * Convert Prism Provider enum to integration string.
     */
    public function providerToIntegration(Provider $provider): string
    {
        return $provider->value;
    }
}
