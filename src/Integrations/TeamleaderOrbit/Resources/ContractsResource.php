<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contracts\ContractsGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contracts\ContractsGetVariableRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contracts\ContractsSetVariableRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contracts\ContractsGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contracts\ContractsGetVariableRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contracts\ContractsListRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contracts\ContractsSetVariableRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class ContractsResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function get(ContractsGetRequestData $data): Response
    {
        return $this->connector->send(new ContractsGetRequest($data));
    }

    public function list(ListRequestData $data = new ListRequestData): Response
    {
        return $this->connector->send(new ContractsListRequest($data));
    }

    public function getVariable(ContractsGetVariableRequestData $data): Response
    {
        return $this->connector->send(new ContractsGetVariableRequest($data));
    }

    public function setVariable(ContractsSetVariableRequestData $data): Response
    {
        return $this->connector->send(new ContractsSetVariableRequest($data));
    }
}
