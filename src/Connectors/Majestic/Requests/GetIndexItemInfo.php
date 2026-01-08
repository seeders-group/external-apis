<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Majestic\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetIndexItemInfo extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(public string $domain) {}

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/';
    }

    protected function defaultQuery(): array
    {
        return [
            'app_api_key' => config('external-apis.majestic.api_key'),
            'cmd' => 'GetIndexItemInfo',
            'items' => 1,
            'item0' => str($this->domain)
                ->remove('https://')
                ->remove('http://')
                ->toString(),
            'datasource' => 'fresh',
        ];
    }
}
