<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Reddit\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class UserAboutRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public string $username) {}

    public function resolveEndpoint(): string
    {
        return '/user/'.$this->username.'/about.json';
    }
}
