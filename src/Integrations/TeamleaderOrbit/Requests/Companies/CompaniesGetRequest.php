<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Companies;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Companies\CompaniesGetRequestData;

class CompaniesGetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public CompaniesGetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/companies.get';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
