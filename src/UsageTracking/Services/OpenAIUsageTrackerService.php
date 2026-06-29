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
        $logModel = UsageTracking::$apiUsageLogModel;

        return $logModel::create([
            'integration' => 'openai',
            'model' => $model,
            'endpoint' => 'images.generations',
            'images_generated' => $imagesGenerated,
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
            'feature' => $context['feature'] ?? 'unknown',
            'sub_feature' => $context['sub_feature'] ?? null,
            'project_id' => $context['project_id'] ?? null,
            'user_id' => $context['user_id'] ?? auth()->id(),
            'status' => 'error',
            'error_message' => $errorMessage,
            'metadata' => $context['metadata'] ?? null,
        ]);
    }
}
