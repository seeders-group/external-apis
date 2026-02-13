<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Clients;

use OpenAI\Responses\Chat\CreateResponse;
use OpenAI;
use OpenAI\Client;
use Seeders\ExternalApis\UsageTracking\Traits\TracksOpenAIUsage;

final class ImageGenerationClient
{
    use TracksOpenAIUsage;

    public Client $client;

    public function __construct()
    {
        $key = config('external-apis.openai.key');

        $this->client = OpenAI::client($key);
    }

    public function generate(string $prompt, array $context = []): ?string
    {
        $response = $this->trackUsage(
            context: array_merge([
                'feature' => 'image_generation',
                'model' => 'gpt-3.5-turbo-1106',
                'endpoint' => 'chat.completions',
            ], $context),
            callback: fn (): CreateResponse => $this->client->chat()->create([
                'model' => 'gpt-3.5-turbo-1106',
                'prompt' => $prompt,
                'n' => 1,
            ])
        );

        return $response->choices[0]->message->content;
    }
}
