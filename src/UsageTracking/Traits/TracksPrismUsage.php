<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Traits;

use Prism\Prism\Embeddings\Response as EmbeddingsResponse;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Structured\Response as StructuredResponse;
use Prism\Prism\Text\Response as TextResponse;
use Seeders\ExternalApis\UsageTracking\Services\PrismUsageTrackerService;
use Throwable;

trait TracksPrismUsage
{
    /**
     * Track usage for a Prism API call.
     *
     * @param  array{feature: string, sub_feature?: string, model?: string, project_id?: int, user_id?: int, metadata?: array}  $context
     * @param  callable  $callback  The Prism API call to execute
     * @return TextResponse|StructuredResponse|EmbeddingsResponse|mixed
     */
    protected function trackPrismUsage(Provider $provider, array $context, callable $callback): mixed
    {
        $tracker = resolve(PrismUsageTrackerService::class);

        try {
            $response = $callback();

            $this->logPrismResponse($tracker, $provider, $response, $context);

            return $response;
        } catch (Throwable $e) {
            $tracker->logError(
                provider: $provider,
                model: $context['model'] ?? 'unknown',
                errorMessage: $e->getMessage(),
                context: $context
            );

            throw $e;
        }
    }

    /**
     * Log usage from a Prism response.
     */
    protected function logPrismResponse(
        PrismUsageTrackerService $tracker,
        Provider $provider,
        mixed $response,
        array $context
    ): void {
        if ($response instanceof TextResponse) {
            $tracker->logRequest(
                provider: $provider,
                model: $response->meta->model,
                promptTokens: $response->usage->promptTokens,
                completionTokens: $response->usage->completionTokens,
                context: $context,
                cachedTokens: $response->usage->cacheReadInputTokens ?? 0,
                thoughtTokens: $response->usage->thoughtTokens,
                requestId: $response->meta->id,
                endpoint: 'text'
            );

            return;
        }

        if ($response instanceof StructuredResponse) {
            $tracker->logRequest(
                provider: $provider,
                model: $response->meta->model,
                promptTokens: $response->usage->promptTokens,
                completionTokens: $response->usage->completionTokens,
                context: $context,
                cachedTokens: $response->usage->cacheReadInputTokens ?? 0,
                thoughtTokens: $response->usage->thoughtTokens,
                requestId: $response->meta->id,
                endpoint: 'structured'
            );

            return;
        }

        if ($response instanceof EmbeddingsResponse) {
            $tracker->logEmbeddingsRequest(
                provider: $provider,
                model: $response->meta->model ?? $context['model'] ?? 'unknown',
                tokensUsed: $response->usage->tokens ?? 0,
                context: $context,
                requestId: $response->meta->id ?? null
            );
        }
    }
}
