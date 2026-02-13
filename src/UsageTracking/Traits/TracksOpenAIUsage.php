<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Traits;

use Seeders\ExternalApis\UsageTracking\Services\OpenAIUsageTrackerService;
use Throwable;

trait TracksOpenAIUsage
{
    protected function trackUsage(array $context, callable $callback)
    {
        $tracker = resolve(OpenAIUsageTrackerService::class);

        try {
            $response = $callback();

            $usage = $this->extractUsageFromResponse($response);

            if ($usage) {
                $tracker->logRequest(
                    model: $usage['model'] ?? $context['model'] ?? 'unknown',
                    promptTokens: $usage['prompt_tokens'] ?? 0,
                    completionTokens: $usage['completion_tokens'] ?? 0,
                    context: $context,
                    cachedTokens: $usage['cached_tokens'] ?? 0,
                    requestId: $usage['request_id'] ?? null,
                    endpoint: $context['endpoint'] ?? 'chat.completions'
                );
            }

            return $response;
        } catch (Throwable $e) {
            $tracker->logError(
                model: $context['model'] ?? 'unknown',
                errorMessage: $e->getMessage(),
                context: $context
            );

            throw $e;
        }
    }

    protected function extractUsageFromResponse($response): ?array
    {
        if (is_object($response)) {
            if (isset($response->usage)) {
                $promptTokens = $response->usage->promptTokens
                    ?? $response->usage->inputTokens
                    ?? $response->usage->prompt_tokens
                    ?? 0;

                $completionTokens = $response->usage->completionTokens
                    ?? $response->usage->outputTokens
                    ?? $response->usage->completion_tokens
                    ?? 0;

                $cachedTokens = $response->usage->promptTokensDetails->cachedTokens
                    ?? $response->usage->inputTokensDetails->cachedTokens
                    ?? $response->usage->prompt_tokens_details->cached_tokens
                    ?? 0;

                return [
                    'model' => $response->model ?? null,
                    'prompt_tokens' => $promptTokens,
                    'completion_tokens' => $completionTokens,
                    'cached_tokens' => $cachedTokens,
                    'request_id' => $response->id ?? null,
                ];
            }

            if (isset($response->data) && is_array($response->data)) {
                return [
                    'model' => $response->model ?? 'dall-e-3',
                    'images_generated' => count($response->data),
                ];
            }
        }

        if (is_array($response) && isset($response['usage'])) {
            return [
                'model' => $response['model'] ?? null,
                'prompt_tokens' => $response['usage']['prompt_tokens'] ?? 0,
                'completion_tokens' => $response['usage']['completion_tokens'] ?? 0,
                'cached_tokens' => $response['usage']['prompt_tokens_details']['cached_tokens'] ?? 0,
                'request_id' => $response['id'] ?? null,
            ];
        }

        return null;
    }
}
