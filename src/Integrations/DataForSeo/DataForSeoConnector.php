<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo;

use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\HasTimeout;

class DataForSeoConnector extends Connector
{
    use AcceptsJson;
    use HasTimeout;

    protected int $connectTimeout = 60;

    protected int $requestTimeout = 120;

    protected function defaultAuth(): ?Authenticator
    {
        $username = config('external-apis.dataforseo.username');
        $password = config('external-apis.dataforseo.password');

        if (! $username || ! $password) {
            return null; // No auth for tests
        }

        return new BasicAuthenticator(
            username: $username,
            password: $password,
        );
    }

    public function resolveBaseUrl(): string
    {
        return 'https://api.dataforseo.com/v3';
    }

    protected function defaultHeaders(): array
    {
        return [];
    }

    protected function defaultConfig(): array
    {
        return [];
    }
}
