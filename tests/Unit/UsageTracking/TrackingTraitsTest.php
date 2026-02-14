<?php

declare(strict_types=1);

use Prism\Prism\Embeddings\Response as EmbeddingsResponse;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Structured\Response as StructuredResponse;
use Prism\Prism\Text\Response as TextResponse;
use Prism\Prism\ValueObjects\Embedding;
use Prism\Prism\ValueObjects\EmbeddingsUsage;
use Prism\Prism\ValueObjects\Meta;
use Prism\Prism\ValueObjects\Usage;
use Seeders\ExternalApis\UsageTracking\Services\OpenAIUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\PrismUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Traits\TracksOpenAIUsage;
use Seeders\ExternalApis\UsageTracking\Traits\TracksPrismUsage;

afterEach(function (): void {
    \Mockery::close();
});

it('tracks openai success and error paths', function (): void {
    $tracker = \Mockery::mock(OpenAIUsageTrackerService::class);
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

    expect(fn () => $client->runTrackUsage(['feature' => 'assistant', 'model' => 'gpt-4o'], function (): never {
        throw new \RuntimeException('failed');
    }))->toThrow(\RuntimeException::class, 'failed');
});

it('tracks prism text, structured, embeddings, and error paths', function (): void {
    $tracker = \Mockery::mock(PrismUsageTrackerService::class);
    $tracker->shouldReceive('logRequest')->twice();
    $tracker->shouldReceive('logEmbeddingsRequest')->once();
    $tracker->shouldReceive('logError')->once()->withArgs(
        fn (Provider $provider, string $model, string $errorMessage): bool => $provider === Provider::OpenAI
            && $model === 'gpt-prism'
            && $errorMessage === 'prism failed'
    );

    app()->instance(PrismUsageTrackerService::class, $tracker);

    $client = new class
    {
        use TracksPrismUsage;

        public function runTrackPrismUsage(Provider $provider, array $context, callable $callback): mixed
        {
            return $this->trackPrismUsage($provider, $context, $callback);
        }
    };

    $usage = new Usage(promptTokens: 100, completionTokens: 20, cacheReadInputTokens: 10, thoughtTokens: 5);
    $meta = new Meta(id: 'req-1', model: 'gpt-prism');

    $textResponse = new TextResponse(
        steps: collect(),
        text: 'hello',
        finishReason: FinishReason::Stop,
        toolCalls: [],
        toolResults: [],
        usage: $usage,
        meta: $meta,
        messages: collect(),
    );

    $structuredResponse = new StructuredResponse(
        steps: collect(),
        text: 'structured',
        structured: ['ok' => true],
        finishReason: FinishReason::Stop,
        usage: $usage,
        meta: $meta,
    );

    $embeddingsResponse = new EmbeddingsResponse(
        embeddings: [new Embedding([0.1, 0.2])],
        usage: new EmbeddingsUsage(tokens: 200),
        meta: new Meta(id: 'req-2', model: 'embed-prism')
    );

    $textResult = $client->runTrackPrismUsage(Provider::OpenAI, ['feature' => 'assistant'], fn () => $textResponse);
    $structuredResult = $client->runTrackPrismUsage(Provider::OpenAI, ['feature' => 'assistant'], fn () => $structuredResponse);
    $embeddingsResult = $client->runTrackPrismUsage(Provider::OpenAI, ['feature' => 'assistant'], fn () => $embeddingsResponse);

    expect($textResult)->toBe($textResponse);
    expect($structuredResult)->toBe($structuredResponse);
    expect($embeddingsResult)->toBe($embeddingsResponse);

    expect(fn () => $client->runTrackPrismUsage(
        Provider::OpenAI,
        ['feature' => 'assistant', 'model' => 'gpt-prism'],
        function (): never {
            throw new \RuntimeException('prism failed');
        }
    ))->toThrow(\RuntimeException::class, 'prism failed');
});
