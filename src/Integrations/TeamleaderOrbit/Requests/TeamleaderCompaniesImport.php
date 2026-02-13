<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class TeamleaderCompaniesImport extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected array $filters = [],
        protected string $orderField = 'name',
        protected string $orderDirection = 'asc',
        protected int $pageSize = 50,
        protected int $pageNumber = 0
    ) {}

    public function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/companies.list';
    }

    protected function defaultBody(): array
    {
        return [
            'filter' => (object) $this->filters,
            'order' => [
                [
                    'field' => $this->orderField,
                    'order' => $this->orderDirection,
                ],
            ],
            'page' => [
                'size' => $this->pageSize,
                'number' => $this->pageNumber,
            ],
        ];
    }
}
