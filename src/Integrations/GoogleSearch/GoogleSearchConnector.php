<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\GoogleSearch;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class GoogleSearchConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://www.googleapis.com';
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }

    protected function defaultQuery(): array
    {
        return [
            'key' => config('external-apis.google_search.key'),
            'cx' => config('external-apis.google_search.cx'),
        ];
    }
}
