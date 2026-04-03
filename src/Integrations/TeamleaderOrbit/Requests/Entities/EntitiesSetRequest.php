<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Entities;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Entities\EntitiesSetRequestData;

class EntitiesSetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public EntitiesSetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/entities.set';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
