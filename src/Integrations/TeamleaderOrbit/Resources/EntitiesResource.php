<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Entities\EntitiesGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Entities\EntitiesSetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Entities\EntitiesContextRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Entities\EntitiesGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Entities\EntitiesListRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Entities\EntitiesSetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class EntitiesResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function get(EntitiesGetRequestData $data): Response
    {
        return $this->connector->send(new EntitiesGetRequest($data));
    }

    public function list(ListRequestData $data = new ListRequestData): Response
    {
        return $this->connector->send(new EntitiesListRequest($data));
    }

    public function set(EntitiesSetRequestData $data): Response
    {
        return $this->connector->send(new EntitiesSetRequest($data));
    }

    public function context(): Response
    {
        return $this->connector->send(new EntitiesContextRequest);
    }
}
