<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\AgentResponse;
use Seeders\ExternalApis\UsageTracking\Services\AiUsageTrackerService;
use Throwable;

class TrackAiUsage
{
    /**
     * @param  array<string, mixed>  $extraContext
     */
    public function __construct(
        private readonly string $feature,
        private readonly string $subFeature = '',
        private readonly ?Model $trackable = null,
        private readonly array $extraContext = [],
    ) {}

    public function handle(AgentPrompt $prompt, Closure $next): AgentResponse
    {
        return $next($prompt)->then(function (AgentResponse $response): void {
            try {
                $tracker = resolve(AiUsageTrackerService::class);

                $context = [
                    'feature' => $this->feature,
                    'sub_feature' => $this->subFeature,
                    ...$this->extraContext,
                ];

                if ($this->trackable instanceof Model) {
                    $context['trackable_type'] = $this->trackable->getMorphClass();
                    $context['trackable_id'] = $this->trackable->getKey();
                }

                $tracker->logRequest(
                    provider: $response->meta->provider ?? 'unknown',
                    model: $response->meta->model ?? 'unknown',
                    promptTokens: $response->usage->promptTokens,
                    completionTokens: $response->usage->completionTokens,
                    context: $context,
                    cachedTokens: $response->usage->cacheReadInputTokens,
                    reasoningTokens: $response->usage->reasoningTokens > 0 ? $response->usage->reasoningTokens : null,
                    requestId: $response->invocationId,
                    endpoint: 'agent',
                );
            } catch (Throwable $e) {
                Log::warning('TrackAiUsage: failed to log usage', [
                    'error' => $e->getMessage(),
                    'feature' => $this->feature,
                ]);
            }
        });
    }
}
