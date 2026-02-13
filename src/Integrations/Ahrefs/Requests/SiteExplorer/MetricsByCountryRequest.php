<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer\MetricsByCountryRequestData;

class MetricsByCountryRequest extends Request
{
    public function __construct(public MetricsByCountryRequestData $data) {}

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/site-explorer/metrics-by-country';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArray();
    }
}
