<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Leolytics;

use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class LeolyticsConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://www.leolytics.com/api/v2';
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

    protected function defaultAuth(): BasicAuthenticator
    {
        return new BasicAuthenticator(
            username: config('external-apis.leolytics.username'),
            password: config('external-apis.leolytics.password'),
        );
    }
}
