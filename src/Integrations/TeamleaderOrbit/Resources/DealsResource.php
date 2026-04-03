<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Deals\DealsGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Deals\DealsSetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Deals\DealsContextRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Deals\DealsGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Deals\DealsListRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Deals\DealsSetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class DealsResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function get(DealsGetRequestData $data): Response
    {
        return $this->connector->send(new DealsGetRequest($data));
    }

    public function list(ListRequestData $data = new ListRequestData): Response
    {
        return $this->connector->send(new DealsListRequest($data));
    }

    public function set(DealsSetRequestData $data): Response
    {
        return $this->connector->send(new DealsSetRequest($data));
    }

    public function context(): Response
    {
        return $this->connector->send(new DealsContextRequest);
    }
}
