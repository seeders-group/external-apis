<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Integrations\Ahrefs\Data\SerpOverview\SerpOverviewRequestData;

class SerpOverviewRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public SerpOverviewRequestData $data,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/serp-overview/serp-overview';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArray();
    }
}
