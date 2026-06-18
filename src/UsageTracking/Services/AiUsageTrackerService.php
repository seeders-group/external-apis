<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Services;

use Seeders\ExternalApis\UsageTracking\UsageTracking;

class AiUsageTrackerService
{
    /**
     * Log a laravel/ai text/structured/agent generation request.
     *
     * @param  array<string, mixed>  $context
     */
    public function logRequest(
        string $provider,
        string $model,
        int $promptTokens,
        int $completionTokens,
        array $context,
        int $cachedTokens = 0,
        ?int $reasoningTokens = null,
        ?string $requestId = null,
        string $endpoint = 'text'
    ) {
        $metadata = $context['metadata'] ?? [];
        if ($reasoningTokens !== null && $reasoningTokens > 0) {
            $metadata['reasoning_tokens'] = $reasoningTokens;
        }

        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $this->normalizeProvider($provider),
            'request_id' => $requestId,
            'model' => $model,
            'endpoint' => "ai.{$endpoint}",
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
     * Log a laravel/ai embeddings request.
     *
     * @param  array<string, mixed>  $context
     */
    public function logEmbeddingsRequest(
        string $provider,
        string $model,
        int $tokensUsed,
        array $context,
        ?string $requestId = null
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $this->normalizeProvider($provider),
            'request_id' => $requestId,
            'model' => $model,
            'endpoint' => 'ai.embeddings',
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
     * Log a laravel/ai image generation request.
     *
     * @param  array<string, mixed>  $context
     */
    public function logImageGeneration(
        string $provider,
        string $model,
        int $imagesGenerated,
        array $context,
        ?string $size = null,
        ?string $quality = null
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $this->normalizeProvider($provider),
            'model' => $model,
            'endpoint' => 'ai.images',
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
     * Log a laravel/ai audio request (TTS or STT).
     *
     * @param  array<string, mixed>  $context
     */
    public function logAudioRequest(
        string $provider,
        string $model,
        array $context,
        ?int $charactersProcessed = null,
        ?int $secondsProcessed = null,
        ?string $requestId = null
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $this->normalizeProvider($provider),
            'request_id' => $requestId,
            'model' => $model,
            'endpoint' => 'ai.audio',
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
     * Log a laravel/ai error.
     *
     * @param  array<string, mixed>  $context
     */
    public function logError(
        string $provider,
        string $model,
        string $errorMessage,
        array $context
    ) {
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => $this->normalizeProvider($provider),
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

    public function normalizeProvider(string $provider): string
    {
        return strtolower($provider);
    }
}
