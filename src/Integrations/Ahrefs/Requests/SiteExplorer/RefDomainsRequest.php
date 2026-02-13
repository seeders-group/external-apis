<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Data\Ahrefs\SiteExplorer\RefdomainsRequestData;

class RefDomainsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public RefdomainsRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/site-explorer/refdomains';
    }

    protected function defaultQuery(): array
    {
        return array_filter($this->data->toArray(), fn ($value): bool => ! is_null($value));
    }
}
