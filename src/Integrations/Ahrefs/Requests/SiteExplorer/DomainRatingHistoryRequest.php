<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer\DomainRatingHistoryRequestData;

class DomainRatingHistoryRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public DomainRatingHistoryRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/site-explorer/domain-rating-history';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArray();
    }
}
