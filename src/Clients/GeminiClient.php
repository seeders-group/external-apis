<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Clients;

use Gemini;
use Gemini\Client;
use Gemini\Data\SafetySetting;
use Gemini\Enums\HarmBlockThreshold;
use Gemini\Enums\HarmCategory;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;

final class GeminiClient
{
    public Client $client;

    private SafetySetting $safetySetting;

    public function __construct()
    {
        $key = config('external-apis.gemini.key');

        $this->safetySetting = new SafetySetting(
            category: HarmCategory::HARM_CATEGORY_SEXUALLY_EXPLICIT,
            threshold: HarmBlockThreshold::BLOCK_NONE
        );

        $this->client = Gemini::client($key);
    }

    public function prompt(string $question, string $model): GenerateContentResponse
    {
        return $this->client
            ->generativeModel(model: $model)
            ->withSafetySetting($this->safetySetting)
            ->withSafetySetting($this->safetySetting)
            ->generateContent($question);
    }
}
