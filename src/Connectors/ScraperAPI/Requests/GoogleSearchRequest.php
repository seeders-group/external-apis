<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\ScraperAPI\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

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
        return [
            'api_key' => config('external-apis.scraperapi.key'),
            'query' => $this->searchQuery,
        ];
    }
}
