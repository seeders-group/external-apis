<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Connectors\WhitePress;

use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class WhitePressConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://www.whitepress.com/customer-api';
    }

    protected function defaultAuth(): ?Authenticator
    {
        return new TokenAuthenticator(config('external-apis.whitepress.api_key'));
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [];
    }
}
