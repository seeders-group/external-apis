<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Deals;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Deals\DealsSetRequestData;

class DealsSetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public DealsSetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/deals.set';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
