<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Pos\PosGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Pos\PosSetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Pos\PosGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Pos\PosSetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class PosResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function get(PosGetRequestData $data): Response
    {
        return $this->connector->send(new PosGetRequest($data));
    }

    public function set(PosSetRequestData $data): Response
    {
        return $this->connector->send(new PosSetRequest($data));
    }
}
