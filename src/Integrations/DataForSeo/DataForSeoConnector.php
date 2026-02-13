<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo;

use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\HasTimeout;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;

class DataForSeoConnector extends Connector
{
    use AcceptsJson;
    use HasTimeout;

    protected int $connectTimeout = 60;

    protected int $requestTimeout = 120;

    protected function defaultAuth(): BasicAuthenticator
    {
        $username = config('external-apis.dataforseo.username');
        $password = config('external-apis.dataforseo.password');

        if (empty($username)) {
            throw new MissingConfigurationException('external-apis.dataforseo.username');
        }

        if (empty($password)) {
            throw new MissingConfigurationException('external-apis.dataforseo.password');
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
