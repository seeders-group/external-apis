<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Requests\Serp\Google\Maps;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp\Google\Maps\LiveRequestData;

class OverviewLiveRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected LiveRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/serp/google/maps/live/advanced';
    }

    protected function defaultBody(): array
    {
        // Drop null fields so an unused location_name/location_code is not sent
        // (DataForSEO accepts either, but rejects a null location_name).
        return [
            array_filter($this->data->toArray(), static fn (mixed $value): bool => $value !== null),
        ];
    }
}
