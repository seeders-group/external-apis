<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Data\Ahrefs\SiteExplorer\RefDomainHistoryRequestData;

class RefdomainHistory extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public RefDomainHistoryRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/site-explorer/refdomains-history';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArray();
    }
}
