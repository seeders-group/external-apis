<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Requests\Users;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class UsersMeRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/users.me';
    }
}
