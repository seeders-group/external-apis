<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Hunter;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;

class HunterConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.hunter.io/v2';
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

    /**
     * Default query parameters for every request
     */
    protected function defaultQuery(): array
    {
        $apiKey = config('external-apis.hunter.api_key');

        if (empty($apiKey)) {
            throw new MissingConfigurationException('external-apis.hunter.api_key');
        }

        return [
            'api_key' => $apiKey,
        ];
    }
}
