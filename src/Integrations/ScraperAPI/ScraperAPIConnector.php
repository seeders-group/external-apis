<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\ScraperAPI;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\HasTimeout;
use Seeders\ExternalApis\UsageTracking\Traits\TracksApiUsage;

class ScraperAPIConnector extends Connector
{
    use AcceptsJson;
    use HasTimeout;
    use TracksApiUsage;

    protected int $connectTimeout = 10;

    protected int $requestTimeout = 120; // Allow 2 minutes for scraping large sites

    public function getIntegrationName(): string
    {
        return 'scraperapi';
    }

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'http://api.scraperapi.com';
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
