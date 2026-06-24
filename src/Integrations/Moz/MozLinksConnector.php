<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Moz;

use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;
use Seeders\ExternalApis\UsageTracking\Traits\TracksApiUsage;

class MozLinksConnector extends Connector
{
    use AcceptsJson;
    use TracksApiUsage;

    public function getIntegrationName(): string
    {
        return 'moz';
    }

    protected function defaultAuth(): BasicAuthenticator
    {
        $clientId = config('external-apis.moz.client_id');
        $clientSecret = config('external-apis.moz.client_secret');

        if (empty($clientId)) {
            throw new MissingConfigurationException('external-apis.moz.client_id');
        }

        if (empty($clientSecret)) {
            throw new MissingConfigurationException('external-apis.moz.client_secret');
        }

        return new BasicAuthenticator($clientId, $clientSecret);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://lsapi.seomoz.com/v2';
    }
}
