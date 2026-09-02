<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectPeriodGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects\ProjectsGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Projects\ProjectPeriodGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Projects\ProjectsGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Projects\ProjectsListRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class ProjectsResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function get(ProjectsGetRequestData $data): Response
    {
        return $this->connector->send(new ProjectsGetRequest($data));
    }

    public function list(ListRequestData $data = new ListRequestData): Response
    {
        return $this->connector->send(new ProjectsListRequest($data));
    }

    public function getPeriod(ProjectPeriodGetRequestData $data): Response
    {
        return $this->connector->send(new ProjectPeriodGetRequest($data));
    }
}
