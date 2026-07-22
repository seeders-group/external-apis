<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Companies;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Companies\CompanyResponseData;

class CompaniesListRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public ListRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/companies.list';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }

    /**
     * @return array<int, CompanyResponseData>
     */
    public function createDtoFromResponse(Response $response): array
    {
        /** @var array<int, array<string, mixed>> $companies */
        $companies = $response->json();

        return array_map(
            fn (array $company): CompanyResponseData => CompanyResponseData::from($company),
            $companies,
        );
    }
}
