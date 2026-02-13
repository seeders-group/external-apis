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
        return [
            $this->data->toArray(),
        ];
    }
}
