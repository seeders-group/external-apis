<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Clients;

use GuzzleHttp\Client;
use OpenAI;
use OpenAI\Client as OpenAIClientContract;
use OpenAI\Responses\Chat\CreateResponse;
use Seeders\ExternalApis\UsageTracking\Traits\TracksOpenAIUsage;

final class OpenAIJsonClient
{
    use TracksOpenAIUsage;

    public OpenAIClientContract $client;

    public function __construct()
    {
        $key = config('external-apis.openai.key');

        $this->client = OpenAI::factory()
            ->withApiKey($key)
            ->withHttpClient(new Client(['timeout' => 120]))
            ->make();
    }

    public function prompt(array $prompt, string $model, array $context = []): ?string
    {
        $response = $this->trackUsage(
            context: array_merge([
                'feature' => 'json_generation',
                'model' => $model,
                'endpoint' => 'chat.completions',
            ], $context),
            callback: fn (): CreateResponse => $this->client->chat()->create([
                'model' => $model,
                'messages' => $prompt,
                'response_format' => [
                    'type' => 'json_object',
                ],
            ])
        );

        return $response->choices[0]->message->content;
    }
}
