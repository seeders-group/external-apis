<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Clients;

use GuzzleHttp\Client;
use OpenAI;
use OpenAI\Client as OpenAIClientContract;
use OpenAI\Responses\Chat\CreateResponse;
use Seeders\ExternalApis\UsageTracking\Traits\TracksOpenAIUsage;

final class DocumentSectionTextGeneratorClient
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

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $context
     */
    public function analyzeData(string $prompt, array $data, string $language, array $context = []): ?string
    {
        $response = $this->trackUsage(
            context: array_merge([
                'feature' => 'document_generation',
                'sub_feature' => 'analyze_data',
                'model' => 'gpt-4o',
                'endpoint' => 'chat.completions',
            ], $context),
            callback: fn (): CreateResponse => $this->client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You will respond in HTML format. You will analyze the given data and summarize it in a short paragraph for people who do not understand the topic, the writing style is semi-informal. Also briefly describe what is going well, and what not. You will write in '.$language.', but do not translate the labels given in the data.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt."\n\n".json_encode($data),
                    ],
                ],
            ])
        );

        return $response->choices[0]->message->content;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $context
     */
    public function generateContent(array $data, string $language, array $context = []): ?string
    {
        $prompt = view('prompts.document-content-generation', ['language' => $language, 'data' => json_encode($data)])->render();
        $response = $this->trackUsage(
            context: array_merge([
                'feature' => 'document_generation',
                'sub_feature' => 'generate_content',
                'model' => 'gpt-4o',
                'endpoint' => 'chat.completions',
            ], $context),
            callback: fn (): CreateResponse => $this->client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => [
                    //                [
                    //                    'role' => 'system',
                    //                    'content' => 'You are a SEO expert',
                    //                ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ])
        );

        return $response->choices[0]->message->content;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $context
     */
    public function generateAnchorAnalysis(array $data, string $domain, string $country, array $context = []): ?string
    {
        $prompt = view('prompts.anchor-profile-analysis', [
            'data' => json_encode($data),
            'domain' => $domain,
            'country' => $country,
        ])->render();

        $response = $this->trackUsage(
            context: array_merge([
                'feature' => 'document_generation',
                'sub_feature' => 'anchor_analysis',
                'model' => 'gpt-4o',
                'endpoint' => 'chat.completions',
            ], $context),
            callback: fn (): CreateResponse => $this->client->chat()->create([
                'model' => 'gpt-4o',
                'response_format' => [
                    'type' => 'json_object',
                ],
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ])
        );

        return $response->choices[0]->message->content;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function analyzeExplanation(string $prompt, string $language, array $context = []): ?string
    {
        $response = $this->trackUsage(
            context: array_merge([
                'feature' => 'document_generation',
                'sub_feature' => 'analyze_explanation',
                'model' => 'gpt-4',
                'endpoint' => 'chat.completions',
            ], $context),
            callback: fn (): CreateResponse => $this->client->chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You will respond in HTML format. You will answer in a short paragraph for people who do not understand the topic, the writing style is semi-informal. You will write in '.$language.', but do not translate the labels given in the data.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ])
        );

        return $response->choices[0]->message->content;
    }
}
