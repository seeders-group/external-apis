<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush;

use Override;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Seeders\ExternalApis\UsageTracking\Semrush\SemrushUsageResolver;
use Seeders\ExternalApis\UsageTracking\Traits\TracksApiUsage;

class SemrushConnector extends Connector
{
    use TracksApiUsage;

    public function resolveBaseUrl(): string
    {
        return 'https://api.semrush.com';
    }

    public function getIntegrationName(): string
    {
        return 'semrush';
    }

    #[Override]
    public function boot(PendingRequest $pendingRequest): void
    {
        if (! config('external-apis.usage_tracking.enabled', true)) {
            return;
        }

        $saloonRequest = $pendingRequest->getRequest();
        $usageResolver = resolve(SemrushUsageResolver::class);
        $usage = $usageResolver->resolve($saloonRequest);

        $pendingRequest->headers()->add('X-Seeders-Expected-Consumption', (string) $usage['units']);
        $pendingRequest->headers()->add('X-Seeders-Expected-Consumption-Type', 'units');
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
