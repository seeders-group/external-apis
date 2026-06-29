<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\GooglePlaces;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Seeders\ExternalApis\Exceptions\MissingConfigurationException;
use Seeders\ExternalApis\UsageTracking\Traits\TracksApiUsage;

class GooglePlacesConnector extends Connector
{
    use AcceptsJson;
    use TracksApiUsage;

    public function getIntegrationName(): string
    {
        return 'google_places';
    }

    public function resolveBaseUrl(): string
    {
        return 'https://maps.googleapis.com';
    }

    protected function defaultHeaders(): array
    {
        return [];
    }

    protected function defaultConfig(): array
    {
        return [];
    }

    protected function defaultQuery(): array
    {
        $key = config('external-apis.google.places_key');

        if (empty($key)) {
            throw new MissingConfigurationException('external-apis.google.places_key');
        }

        return [
            'key' => $key,
        ];
    }
}
