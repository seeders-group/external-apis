<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Moz;

use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class MozLinksConnector extends Connector
{
    use AcceptsJson;

    protected function defaultAuth(): BasicAuthenticator
    {
        return new BasicAuthenticator(
            config('external-apis.moz.client_id'),
            config('external-apis.moz.client_secret'),
        );
    }

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://lsapi.seomoz.com/v2';
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
