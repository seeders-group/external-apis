<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\GoogleSearch;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;

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
        $key = config('external-apis.google_search.key');
        $cx = config('external-apis.google_search.cx');

        if (empty($key)) {
            throw new MissingConfigurationException('external-apis.google_search.key');
        }

        if (empty($cx)) {
            throw new MissingConfigurationException('external-apis.google_search.cx');
        }

        return [
            'key' => $key,
            'cx' => $cx,
        ];
    }
}
