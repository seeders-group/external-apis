<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Semrush\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ApiUnitsBalanceRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/';
    }

    protected function defaultQuery(): array
    {
        return [
            'type' => 'api_units',
            'key' => config('external-apis.semrush.api_key'),
        ];
    }
}
