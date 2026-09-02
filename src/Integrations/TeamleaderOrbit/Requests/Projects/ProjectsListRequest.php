<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Projects;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectResponseData;

class ProjectsListRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public ListRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/projects.list';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }

    /**
     * @return array<int, ProjectResponseData>
     */
    public function createDtoFromResponse(Response $response): array
    {
        /** @var array<int, array<string, mixed>> $projects */
        $projects = $response->json();

        return array_map(
            fn (array $project): ProjectResponseData => ProjectResponseData::from($project),
            $projects,
        );
    }
}
