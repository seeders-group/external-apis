<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ereplace\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class MarketPlaceRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/seeder/get';
    }
}
