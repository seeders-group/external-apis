<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Offers;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Offers\OffersSetRequestData;

class OffersSetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public OffersSetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/offers.set';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
