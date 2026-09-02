<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Projects;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectResponseData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectsGetRequestData;

class ProjectsGetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public ProjectsGetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/projects.get';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }

    public function createDtoFromResponse(Response $response): ProjectResponseData
    {
        return ProjectResponseData::from($response->json());
    }
}
