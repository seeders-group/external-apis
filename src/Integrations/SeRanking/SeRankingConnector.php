<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\SeRanking;

use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;

class SeRankingConnector extends Connector
{
    use AcceptsJson;

    protected function defaultAuth(): HeaderAuthenticator
    {
        $token = config('external-apis.seranking.token');

        if (empty($token)) {
            throw new MissingConfigurationException('external-apis.seranking.token');
        }

        return new HeaderAuthenticator($token, 'Authorization');
    }

    public function resolveBaseUrl(): string
    {
        return 'https://api4.seranking.com';
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
