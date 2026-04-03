<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Resources;

use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common\ListRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contacts\ContactsCheckaddRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contacts\ContactsDetailRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contacts\ContactsGetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contacts\ContactsSetRequestData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contacts\ContactsCheckaddRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contacts\ContactsDetailRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contacts\ContactsGetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contacts\ContactsListRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contacts\ContactsSetRequest;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\TeamleaderOrbitConnector;

class ContactsResource
{
    public function __construct(private readonly TeamleaderOrbitConnector $connector) {}

    public function get(ContactsGetRequestData $data): Response
    {
        return $this->connector->send(new ContactsGetRequest($data));
    }

    public function list(ListRequestData $data = new ListRequestData): Response
    {
        return $this->connector->send(new ContactsListRequest($data));
    }

    public function detail(ContactsDetailRequestData $data): Response
    {
        return $this->connector->send(new ContactsDetailRequest($data));
    }

    public function checkadd(ContactsCheckaddRequestData $data): Response
    {
        return $this->connector->send(new ContactsCheckaddRequest($data));
    }

    public function set(ContactsSetRequestData $data): Response
    {
        return $this->connector->send(new ContactsSetRequest($data));
    }
}
