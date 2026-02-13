<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\ScraperAPI\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;

class GoogleSearchRequest extends Request
{
    public function __construct(public string $searchQuery) {}

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/structured/google/search';
    }

    protected function defaultQuery(): array
    {
        $apiKey = config('external-apis.scraperapi.key');

        if (empty($apiKey)) {
            throw new MissingConfigurationException('external-apis.scraperapi.key');
        }

        return [
            'api_key' => $apiKey,
            'query' => $this->searchQuery,
        ];
    }
}
