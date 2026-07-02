<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Offers\OffersGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Offers\OffersSetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Offers\OffersContextRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Offers\OffersGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Offers\OffersListRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Offers\OffersSetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class OffersResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function get(OffersGetRequestData $data): Response
    {
        return $this->connector->send(new OffersGetRequest($data));
    }

    public function list(ListRequestData $data = new ListRequestData): Response
    {
        return $this->connector->send(new OffersListRequest($data));
    }

    public function set(OffersSetRequestData $data): Response
    {
        return $this->connector->send(new OffersSetRequest($data));
    }

    public function context(): Response
    {
        return $this->connector->send(new OffersContextRequest);
    }
}
