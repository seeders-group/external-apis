<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush;

use Illuminate\Support\Facades\Log;
use Override;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use Seeders\ExternalApis\UsageTracking\Semrush\SemrushUsageResolver;
use Seeders\ExternalApis\UsageTracking\Services\SemrushUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Traits\TracksApiUsage;
use Throwable;

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

        // Provide generic consumption hints for RecordApiUsage middleware.
        $pendingRequest->headers()->add('X-Seeders-Expected-Consumption', (string) $usage['units']);
        $pendingRequest->headers()->add('X-Seeders-Expected-Consumption-Type', 'units');

        $pendingRequest->middleware()->onResponse(function (Response $response) use ($saloonRequest, $usage): void {
            try {
                $tracker = resolve(SemrushUsageTrackerService::class);

                if ($response->successful()) {
                    $tracker->logRequest(
                        endpoint: $saloonRequest->resolveEndpoint(),
                        requestType: $usage['request_type'],
                        unitsConsumed: $usage['units'],
                        context: [
                            'feature' => $usage['feature'],
                            'target_count' => $usage['target_count'],
                            'metadata' => [
                                'status_code' => $response->status(),
                            ],
                        ],
                    );

                    return;
                }

                $tracker->logError(
                    endpoint: $saloonRequest->resolveEndpoint(),
                    requestType: $usage['request_type'],
                    errorMessage: $response->body(),
                    context: [
                        'feature' => $usage['feature'],
                        'target_count' => $usage['target_count'],
                        'metadata' => [
                            'status_code' => $response->status(),
                        ],
                    ],
                );
            } catch (Throwable $e) {
                Log::warning('Failed to record Semrush usage log: '.$e->getMessage());
            }
        });
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
