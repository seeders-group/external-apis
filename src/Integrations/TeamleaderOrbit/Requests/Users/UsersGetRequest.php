<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Users;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Users\UsersGetRequestData;

class UsersGetRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(public UsersGetRequestData $data) {}

    public function resolveEndpoint(): string
    {
        return '/users.get';
    }

    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
