<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Deals;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;

class DealsListRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public ListRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/deals.list';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
