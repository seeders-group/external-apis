<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Projects;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectPeriodGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectPeriodResponseData;

class ProjectPeriodGetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public ProjectPeriodGetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/projectperiod.get';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }

    public function createDtoFromResponse(Response $response): ProjectPeriodResponseData
    {
        return ProjectPeriodResponseData::from($response->json());
    }
}
