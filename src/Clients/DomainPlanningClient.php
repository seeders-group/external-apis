<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Clients;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\View;
use OpenAI;
use OpenAI\Client as OpenAIClientContract;
use OpenAI\Responses\Chat\CreateResponse;
use Seeders\ExternalApis\UsageTracking\Contracts\PlanningInterface;
use Seeders\ExternalApis\UsageTracking\Traits\TracksOpenAIUsage;

final class DomainPlanningClient
{
    use TracksOpenAIUsage;

    public OpenAIClientContract $client;

    public function __construct()
    {
        $key = config('external-apis.openai.key');

        $this->client = OpenAI::factory()
            ->withApiKey($key)
            ->withHttpClient(new Client(['timeout' => 160]))
            ->make();
    }

    public function execute(PlanningInterface $planning, string $domainString, array $context = []): mixed
    {
        $userPrompt = View::make('prompts.domain-planning', [
            'planning' => $planning,
            'domains' => $domainString,
        ])->render();

        $systemPrompt = View::make('prompts.domain-planning-system')->render();

        return $this->trackUsage(
            context: array_merge([
                'feature' => 'domain_planning',
                'model' => 'gpt-4o',
                'endpoint' => 'chat.completions',
            ], $context),
            callback: fn (): CreateResponse => $this->client->chat()->create([
                //            'model' => 'gpt-4-1106-preview',
                'model' => 'gpt-4o',
                'temperature' => 1,
                //            'top_p' => 1,
                //            'n' => 1,
                'stream' => false,
                //            'presence_penalty' => 0,
                //            'frequency_penalty' => 0,
                'response_format' => [
                    'type' => 'json_object',
                ],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
            ])
        );
    }
}
