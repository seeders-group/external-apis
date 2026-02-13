<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class TeamleaderRequestCompanies extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $companyId
    ) {}

    public function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/companies.get';
    }

    protected function defaultBody(): array
    {
        return [
            'id' => $this->companyId,
        ];
    }
}
