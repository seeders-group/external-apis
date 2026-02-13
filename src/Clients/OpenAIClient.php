<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Clients;

use OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use OpenAI\Client as OpenAIClientContract;
use Seeders\ExternalApis\UsageTracking\Traits\TracksOpenAIUsage;

final class OpenAIClient
{
    use TracksOpenAIUsage;

    public OpenAIClientContract $client;

    public function __construct()
    {
        $key = config('external-apis.openai.key');

        $this->client = OpenAI::factory()
            ->withApiKey($key)
            ->withHttpClient(new Client(['timeout' => 300]))
            ->make();
    }

    public function prompt(mixed $questions, string $model, array $config = [], array $context = []): mixed
    {
        return $this->trackUsage(
            context: array_merge([
                'feature' => 'general',
                'model' => $model,
                'endpoint' => 'chat.completions',
            ], $context),
            callback: fn (): CreateResponse => $this->client
                ->chat()
                ->create([
                    'model' => $model,
                    'messages' => [$questions],
                    ...$config,
                ])
        );
    }

    public function createContent(Collection $conversation, array $context = []): mixed
    {
        return $this->trackUsage(
            context: array_merge([
                'feature' => 'content_generation',
                'model' => 'gpt-4o',
                'endpoint' => 'chat.completions',
            ], $context),
            callback: fn (): CreateResponse => $this->client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => $conversation->toArray(),
            ])
        );
    }

    public function responses(string $input, string $model = 'gpt-4o', array $config = [], array $context = []): mixed
    {
        return $this->trackUsage(
            context: array_merge([
                'feature' => 'general',
                'model' => $model,
                'endpoint' => 'responses',
            ], $context),
            callback: fn (): \OpenAI\Responses\Responses\CreateResponse => $this->client
                ->responses()
                ->create([
                    'model' => $model,
                    'input' => $input,
                    ...$config,
                ])
        );
    }

    /**
     * Create a response with web search enabled using Responses API
     */
    public function responsesWithWebSearch(string $input, string $model = 'gpt-5', array $options = [], array $context = []): mixed
    {
        // Create a client with extended timeout for web search (10 minutes)
        // Competitor discovery with web search can take 5-10 minutes
        $webSearchClient = OpenAI::factory()
            ->withApiKey(config('external-apis.openai.key'))
            ->withHttpClient(new Client(['timeout' => 600])) // 10 minutes for web search
            ->make();

        $params = [
            'model' => $model,
            'input' => $input,
            'tools' => [
                ['type' => 'web_search'],
            ],
            'tool_choice' => 'auto',
        ];

        // Merge any additional options (but don't try structured outputs yet)
        $params = array_merge($params, $options);

        return $this->trackUsage(
            context: array_merge([
                'feature' => 'web_search',
                'model' => $model,
                'endpoint' => 'responses.web_search',
            ], $context),
            callback: fn (): \OpenAI\Responses\Responses\CreateResponse => $webSearchClient
                ->responses()
                ->create($params)
        );
    }
}
