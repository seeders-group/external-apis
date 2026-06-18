<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Traits;

use Illuminate\Database\Eloquent\Model;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\EmbeddingsResponse;
use Laravel\Ai\Responses\StructuredTextResponse;
use Laravel\Ai\Responses\TextResponse;
use Seeders\ExternalApis\UsageTracking\Services\AiUsageTrackerService;
use Throwable;

trait TracksAiUsage
{
    /**
     * Track usage for a laravel/ai API call.
     *
     * @param  array{feature: string, sub_feature?: string, model?: string, project_id?: int, user_id?: int, trackable_type?: string, trackable_id?: int, metadata?: array}  $context
     * @param  callable  $callback  The laravel/ai call to execute
     * @param  Model|null  $trackable  Optional model to associate usage with
     */
    protected function trackAiUsage(string $provider, array $context, callable $callback, ?Model $trackable = null): mixed
    {
        if ($trackable instanceof Model) {
            $context['trackable_type'] = $trackable->getMorphClass();
            $context['trackable_id'] = $trackable->getKey();
        }

        $tracker = resolve(AiUsageTrackerService::class);

        try {
            $response = $callback();

            $this->logAiResponse($tracker, $provider, $response, $context);

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
     * Log usage from a laravel/ai response.
     *
     * @param  array<string, mixed>  $context
     */
    protected function logAiResponse(
        AiUsageTrackerService $tracker,
        string $provider,
        mixed $response,
        array $context
    ): void {
        if ($response instanceof EmbeddingsResponse) {
            $tracker->logEmbeddingsRequest(
                provider: $provider,
                model: $response->meta->model ?? $context['model'] ?? 'unknown',
                tokensUsed: $response->tokens,
                context: $context,
            );

            return;
        }

        if ($response instanceof TextResponse) {
            $endpoint = match (true) {
                $response instanceof AgentResponse => 'agent',
                $response instanceof StructuredTextResponse => 'structured',
                default => 'text',
            };

            $tracker->logRequest(
                provider: $provider,
                model: $response->meta->model ?? $context['model'] ?? 'unknown',
                promptTokens: $response->usage->promptTokens,
                completionTokens: $response->usage->completionTokens,
                context: $context,
                cachedTokens: $response->usage->cacheReadInputTokens,
                reasoningTokens: $response->usage->reasoningTokens > 0 ? $response->usage->reasoningTokens : null,
                requestId: $response instanceof AgentResponse ? $response->invocationId : null,
                endpoint: $endpoint,
            );
        }
    }
}
