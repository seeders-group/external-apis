<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\Ahrefs;

use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class AhrefsConnector extends Connector
{
    use AcceptsJson;

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator(
            config('external-apis.ahrefs.token')
        );
    }

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.ahrefs.com/v3';
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
}
