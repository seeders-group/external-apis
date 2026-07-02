<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contracts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contracts\ContractsSetVariableRequestData;

class ContractsSetVariableRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public ContractsSetVariableRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/contracts.setvariable';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
