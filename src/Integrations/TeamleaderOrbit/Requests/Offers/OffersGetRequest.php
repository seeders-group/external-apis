<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Offers;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Offers\OffersGetRequestData;

class OffersGetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public OffersGetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/offers.get';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
