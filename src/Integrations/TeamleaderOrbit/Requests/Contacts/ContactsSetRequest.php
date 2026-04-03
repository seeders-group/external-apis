<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Contacts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contacts\ContactsSetRequestData;

class ContactsSetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public ContactsSetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/contacts.set';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
