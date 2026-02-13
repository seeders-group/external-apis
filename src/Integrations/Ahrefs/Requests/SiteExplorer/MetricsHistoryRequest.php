<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer\MetricsHistoryRequestData;

class MetricsHistoryRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public MetricsHistoryRequestData $data) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/site-explorer/metrics-history';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArray();
    }
}
