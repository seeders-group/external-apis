<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Moz\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Data\Moz\UrlMetrics\UrlMetricsRequestData;

class UrlMetricsRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public UrlMetricsRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/url_metrics';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
