<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Leolytics\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class WebsiteRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/websites';
    }
}
