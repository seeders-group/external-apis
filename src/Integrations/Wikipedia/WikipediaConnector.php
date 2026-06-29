<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Wikipedia;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\HasTimeout;
use Seeders\ExternalApis\UsageTracking\Traits\TracksApiUsage;

class WikipediaConnector extends Connector
{
    use AcceptsJson;
    use HasTimeout;
    use TracksApiUsage;

    protected int $connectTimeout = 10;

    protected int $requestTimeout = 30;

    public function resolveBaseUrl(): string
    {
        return (string) config('external-apis.wikipedia.base_url', 'https://en.wikipedia.org/w/api.php');
    }

    public function getIntegrationName(): string
    {
        return 'wikipedia';
    }

    protected function defaultHeaders(): array
    {
        return [
            'User-Agent' => (string) config('external-apis.user_agent', 'SeedersExternalApis/1.0'),
        ];
    }

    protected function defaultConfig(): array
    {
        $timeout = (int) config('external-apis.wikipedia.timeout', 30);

        return [
            'timeout' => $timeout,
        ];
    }
}
