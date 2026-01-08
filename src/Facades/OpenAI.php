<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Facades;

use Illuminate\Support\Facades\Facade;
use Seeders\ExternalApis\Clients\OpenAIClient;

/**
 * @method static mixed prompt(array $prompt, string $model = 'gpt-4o', array $context = [])
 * @method static mixed createContent(string $prompt, array $context = [])
 * @method static mixed responses(array $prompt, string $model = 'gpt-4o', array $context = [])
 * @method static mixed responsesWithWebSearch(array $prompt, string $model = 'gpt-4o', array $context = [])
 *
 * @see \Seeders\ExternalApis\Clients\OpenAIClient
 */
final class OpenAI extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return OpenAIClient::class;
    }
}
