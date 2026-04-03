<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Companies\CompaniesGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Companies\CompaniesSetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Companies\CompaniesGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Companies\CompaniesListRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Companies\CompaniesSetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class CompaniesResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function get(CompaniesGetRequestData $data): Response
    {
        return $this->connector->send(new CompaniesGetRequest($data));
    }

    public function list(ListRequestData $data = new ListRequestData): Response
    {
        return $this->connector->send(new CompaniesListRequest($data));
    }

    public function set(CompaniesSetRequestData $data): Response
    {
        return $this->connector->send(new CompaniesSetRequest($data));
    }
}
