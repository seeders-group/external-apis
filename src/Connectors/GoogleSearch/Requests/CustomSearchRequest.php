<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\GoogleSearch\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class CustomSearchRequest extends Request
{
    public function __construct(public string $searchQuery, public ?string $gl = null) {}

    protected Method $method = Method::GET;

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/customsearch/v1';
    }

    protected function defaultQuery(): array
    {
        return [
            'q' => $this->searchQuery,
        ];
    }
}
