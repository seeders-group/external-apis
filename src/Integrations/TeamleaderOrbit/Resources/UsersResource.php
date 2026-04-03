<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Users\MailsContextRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Users\UsersGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Users\MailsContextRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Users\UsersGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Users\UsersListRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Users\UsersMeRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class UsersResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function me(): Response
    {
        return $this->connector->send(new UsersMeRequest);
    }

    public function get(UsersGetRequestData $data): Response
    {
        return $this->connector->send(new UsersGetRequest($data));
    }

    public function list(ListRequestData $data = new ListRequestData): Response
    {
        return $this->connector->send(new UsersListRequest($data));
    }

    public function mailsContext(MailsContextRequestData $data): Response
    {
        return $this->connector->send(new MailsContextRequest($data));
    }
}
