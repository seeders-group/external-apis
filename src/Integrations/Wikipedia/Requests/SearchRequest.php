<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Wikipedia\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\Wikipedia\Data\SearchResponseData;
use Seeders\ExternalApis\Integrations\Wikipedia\Data\SearchResultData;

class SearchRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $searchTerm,
        public readonly int $limit = 5,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/';
    }

    protected function defaultQuery(): array
    {
        return [
            'action' => 'query',
            'list' => 'search',
            'srsearch' => $this->searchTerm,
            'format' => 'json',
            'srlimit' => $this->limit,
        ];
    }

    public function createDtoFromResponse(Response $response): SearchResponseData
    {
        /** @var array<string, mixed> $json */
        $json = $response->json();

        /** @var array<int, array<string, mixed>> $searchResults */
        $searchResults = $json['query']['search'] ?? [];

        $results = array_map(
            fn (array $item): SearchResultData => new SearchResultData(
                title: (string) ($item['title'] ?? ''),
                pageid: (int) ($item['pageid'] ?? 0),
                wordcount: (int) ($item['wordcount'] ?? 0),
                snippet: strip_tags((string) ($item['snippet'] ?? '')),
            ),
            $searchResults,
        );

        return new SearchResponseData(
            results: $results,
            totalHits: (int) ($json['query']['searchinfo']['totalhits'] ?? 0),
        );
    }
}
