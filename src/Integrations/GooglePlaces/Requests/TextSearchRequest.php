<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\GooglePlaces\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class TextSearchRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public string $searchQuery) {}

    public function resolveEndpoint(): string
    {
        return '/maps/api/place/textsearch/json';
    }

    protected function defaultQuery(): array
    {
        return [
            'query' => $this->searchQuery,
        ];
    }
}
