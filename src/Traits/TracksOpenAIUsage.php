<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Traits;

use Seeders\ExternalApis\Contracts\UsageTrackerInterface;
use Throwable;

trait TracksOpenAIUsage
{
    protected function trackUsage(array $context, callable $callback): mixed
    {
        $tracker = $this->getUsageTracker();

        try {
            // Execute the OpenAI API call
            $response = $callback();

            // Extract usage data from response
            $usage = $this->extractUsageFromResponse($response);

            if ($tracker && $usage) {
                // Log the request with usage data
                $tracker->logUsage([
                    'model' => $usage['model'] ?? $context['model'] ?? 'unknown',
                    'prompt_tokens' => $usage['prompt_tokens'] ?? 0,
                    'completion_tokens' => $usage['completion_tokens'] ?? 0,
                    'cached_tokens' => $usage['cached_tokens'] ?? 0,
                    'request_id' => $usage['request_id'] ?? null,
                    'endpoint' => $context['endpoint'] ?? 'chat.completions',
                    'context' => $context,
                ]);
            }

            return $response;
        } catch (Throwable $e) {
            // Log error if tracker is available
            if ($tracker) {
                $tracker->logUsage([
                    'model' => $context['model'] ?? 'unknown',
                    'error' => true,
                    'error_message' => $e->getMessage(),
                    'context' => $context,
                ]);
            }

            // Re-throw the exception
            throw $e;
        }
    }

    protected function getUsageTracker(): ?UsageTrackerInterface
    {
        // Try to resolve the usage tracker from the container
        // Returns null if not available (package can work without tracking)
        try {
            if (function_exists('app') && app()->bound(UsageTrackerInterface::class)) {
                return app(UsageTrackerInterface::class);
            }
        } catch (Throwable) {
            // Silently fail if container is not available
        }

        return null;
    }

    protected function extractUsageFromResponse(mixed $response): ?array
    {
        // Handle different OpenAI response types
        if (is_object($response)) {
            // Standard chat completion response
            if (isset($response->usage)) {
                // Chat Completions API uses promptTokens/completionTokens
                // Responses API uses inputTokens/outputTokens
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

            // Image generation response
            if (isset($response->data) && is_array($response->data)) {
                return [
                    'model' => $response->model ?? 'dall-e-3',
                    'images_generated' => count($response->data),
                ];
            }
        }

        // Handle array responses
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
