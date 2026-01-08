<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\DataForSeo\Requests\Serp;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Data\DataForSeo\Serp\GoogleOrganicLiveRequestData;

class GoogleOrganicLiveRequest extends Request implements HasBody
{
    use HasJsonBody;

    public function __construct(public GoogleOrganicLiveRequestData $data) {}

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/serp/google/organic/live/regular';
    }

    protected function defaultBody(): array
    {
        return [
            $this->data->toArray(),
        ];
    }
}
