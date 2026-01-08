<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\TeamleaderOrbit\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class TeamleaderRequestContacts extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $contactId
    ) {}

    public function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/contacts.get';
    }

    protected function defaultBody(): array
    {
        return [
            'id' => $this->contactId,
        ];
    }
}
