<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Reddit;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\HasTimeout;
use Seeders\ExternalApis\UsageTracking\Traits\TracksApiUsage;

/**
 * Connector for Reddit's public, unauthenticated JSON API.
 *
 * Reddit requires a descriptive User-Agent; requests are otherwise anonymous.
 */
class RedditConnector extends Connector
{
    use AcceptsJson;
    use HasTimeout;
    use TracksApiUsage;

    public function getIntegrationName(): string
    {
        return 'reddit';
    }

    public function resolveBaseUrl(): string
    {
        return 'https://www.reddit.com';
    }

    protected function defaultHeaders(): array
    {
        return [
            'User-Agent' => config('external-apis.reddit.user_agent', 'GeoAuditBot/1.0 (brand-monitoring)'),
        ];
    }

    protected function defaultConfig(): array
    {
        return [];
    }

    public function getRequestTimeout(): float
    {
        return (float) config('external-apis.reddit.timeout', 15);
    }
}
