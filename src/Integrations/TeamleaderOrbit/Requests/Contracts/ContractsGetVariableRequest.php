<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contracts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contracts\ContractsGetVariableRequestData;

class ContractsGetVariableRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public ContractsGetVariableRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/contracts.getvariable';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
