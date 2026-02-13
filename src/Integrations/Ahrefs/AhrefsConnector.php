<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs;

use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;

class AhrefsConnector extends Connector
{
    use AcceptsJson;

    protected function defaultAuth(): TokenAuthenticator
    {
        $token = config('external-apis.ahrefs.token');

        if (empty($token)) {
            throw new MissingConfigurationException('external-apis.ahrefs.token');
        }

        return new TokenAuthenticator($token);
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
