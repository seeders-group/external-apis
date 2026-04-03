<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contacts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contacts\ContactsGetRequestData;

class ContactsGetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public ContactsGetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/contacts.get';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
