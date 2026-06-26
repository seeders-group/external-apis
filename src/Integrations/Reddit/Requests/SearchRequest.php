<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Reddit\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class SearchRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $searchQuery,
        public int $limit = 25,
        public string $sort = 'relevance',
        public string $type = 'link',
    ) {}

    public function resolveEndpoint(): string
    {
        return '/search.json';
    }

    protected function defaultQuery(): array
    {
        return [
            'q' => $this->searchQuery,
            'limit' => $this->limit,
            'sort' => $this->sort,
            'type' => $this->type,
        ];
    }
}
