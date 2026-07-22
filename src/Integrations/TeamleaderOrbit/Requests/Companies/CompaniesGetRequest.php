<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Companies;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Companies\CompaniesGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Companies\CompanyResponseData;

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

    public function createDtoFromResponse(Response $response): CompanyResponseData
    {
        return CompanyResponseData::from($response->json());
    }
}
