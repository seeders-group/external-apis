<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Assets\AssetsGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Assets\AssetsSetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Assets\AssetsGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Assets\AssetsListRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Assets\AssetsSetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Assets\AssetTypesListRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class AssetsResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function get(AssetsGetRequestData $data): Response
    {
        return $this->connector->send(new AssetsGetRequest($data));
    }

    public function list(ListRequestData $data = new ListRequestData): Response
    {
        return $this->connector->send(new AssetsListRequest($data));
    }

    public function set(AssetsSetRequestData $data): Response
    {
        return $this->connector->send(new AssetsSetRequest($data));
    }

    public function types(ListRequestData $data = new ListRequestData): Response
    {
        return $this->connector->send(new AssetTypesListRequest($data));
    }
}
