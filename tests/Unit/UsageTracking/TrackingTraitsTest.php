<?php

declare(strict_types=1);

use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\Data\Usage;
use Laravel\Ai\Responses\EmbeddingsResponse;
use Laravel\Ai\Responses\StructuredTextResponse;
use Laravel\Ai\Responses\TextResponse;
use Seeders\ExternalApis\UsageTracking\Services\AiUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\OpenAIUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Traits\TracksAiUsage;
use Seeders\ExternalApis\UsageTracking\Traits\TracksOpenAIUsage;

afterEach(function (): void {
    Mockery::close();
});

it('tracks openai success and error paths', function (): void {
    $tracker = Mockery::mock(OpenAIUsageTrackerService::class);
    $tracker->shouldReceive('logRequest')->once()->withArgs(
        fn (string $model, int $promptTokens, int $completionTokens, array $context, int $cachedTokens, ?string $requestId, string $endpoint): bool => $model === 'gpt-4o'
            && $promptTokens === 10
            && $completionTokens === 5
            && $cachedTokens === 2
            && $requestId === 'resp-1'
            && $endpoint === 'chat.completions'
    );
    $tracker->shouldReceive('logError')->once()->withArgs(
        fn (string $model, string $errorMessage): bool => $model === 'gpt-4o' && $errorMessage === 'failed'
    );

    app()->instance(OpenAIUsageTrackerService::class, $tracker);

    $client = new class
    {
        use TracksOpenAIUsage;

        public function runTrackUsage(array $context, callable $callback): mixed
        {
            return $this->trackUsage($context, $callback);
        }

        public function runExtractUsage(mixed $response): ?array
        {
            return $this->extractUsageFromResponse($response);
        }
    };

    $response = (object) [
        'id' => 'resp-1',
        'model' => 'gpt-4o',
        'usage' => (object) [
            'promptTokens' => 10,
            'completionTokens' => 5,
            'promptTokensDetails' => (object) ['cachedTokens' => 2],
        ],
    ];

    $result = $client->runTrackUsage(
        ['feature' => 'assistant', 'model' => 'gpt-4o', 'endpoint' => 'chat.completions'],
        fn () => $response
    );

    expect($result)->toBe($response);
    expect($client->runExtractUsage([
        'id' => 'arr-1',
        'model' => 'gpt-array',
        'usage' => ['prompt_tokens' => 4, 'completion_tokens' => 6],
    ]))
        ->toMatchArray([
            'request_id' => 'arr-1',
            'model' => 'gpt-array',
            'prompt_tokens' => 4,
            'completion_tokens' => 6,
            'cached_tokens' => 0,
        ]);

    expect(fn (): mixed => $client->runTrackUsage(['feature' => 'assistant', 'model' => 'gpt-4o'], function (): never {
        throw new RuntimeException('failed');
    }))->toThrow(RuntimeException::class, 'failed');
});

it('tracks laravel/ai text, structured, agent, embeddings, and error paths', function (): void {
    $tracker = Mockery::mock(AiUsageTrackerService::class);
    $tracker->shouldReceive('logRequest')->times(3);
    $tracker->shouldReceive('logEmbeddingsRequest')->once();
    $tracker->shouldReceive('logError')->once()->withArgs(
        fn (string $provider, string $model, string $errorMessage): bool => $provider === 'openai'
            && $model === 'gpt-ai'
            && $errorMessage === 'ai failed'
    );

    app()->instance(AiUsageTrackerService::class, $tracker);

    $client = new class
    {
        use TracksAiUsage;

        public function runTrackAiUsage(string $provider, array $context, callable $callback): mixed
        {
            return $this->trackAiUsage($provider, $context, $callback);
        }
    };

    $usage = new Usage(promptTokens: 100, completionTokens: 20, cacheReadInputTokens: 10, reasoningTokens: 5);
    $meta = new Meta(provider: 'openai', model: 'gpt-ai');

    $textResponse = new TextResponse(text: 'hello', usage: $usage, meta: $meta);

    $structuredResponse = new StructuredTextResponse(
        structured: ['ok' => true],
        text: 'structured',
        usage: $usage,
        meta: $meta,
    );

    $agentResponse = new AgentResponse(
        invocationId: 'inv-1',
        text: 'agent',
        usage: $usage,
        meta: $meta,
    );

    $embeddingsResponse = new EmbeddingsResponse(
        embeddings: [[0.1, 0.2]],
        tokens: 200,
        meta: new Meta(provider: 'openai', model: 'embed-ai'),
    );

    $textResult = $client->runTrackAiUsage('openai', ['feature' => 'assistant'], fn (): TextResponse => $textResponse);
    $structuredResult = $client->runTrackAiUsage('openai', ['feature' => 'assistant'], fn (): StructuredTextResponse => $structuredResponse);
    $agentResult = $client->runTrackAiUsage('openai', ['feature' => 'assistant'], fn (): AgentResponse => $agentResponse);
    $embeddingsResult = $client->runTrackAiUsage('openai', ['feature' => 'assistant'], fn (): EmbeddingsResponse => $embeddingsResponse);

    expect($textResult)->toBe($textResponse);
    expect($structuredResult)->toBe($structuredResponse);
    expect($agentResult)->toBe($agentResponse);
    expect($embeddingsResult)->toBe($embeddingsResponse);

    expect(fn (): mixed => $client->runTrackAiUsage(
        'openai',
        ['feature' => 'assistant', 'model' => 'gpt-ai'],
        function (): never {
            throw new RuntimeException('ai failed');
        }
    ))->toThrow(RuntimeException::class, 'ai failed');
})->skip(! class_exists(AgentResponse::class), 'laravel/ai is not installed');
