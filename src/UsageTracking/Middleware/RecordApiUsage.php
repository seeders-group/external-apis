<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Middleware;

use Illuminate\Support\Facades\Log;
use Saloon\Http\Response;
use Seeders\ExternalApis\UsageTracking\UsageTracking;
use Throwable;

class RecordApiUsage
{
    public function __invoke(Response $response): void
    {
        try {
            $request = $response->getPendingRequest();
            $connector = $request->getConnector();

            $saloonRequest = $request->getRequest();
            $endpoint = $saloonRequest->resolveEndpoint();

            $apiLogModel = UsageTracking::$apiLogModel;

            $apiLogModel::create([
                'trackable_type' => $request->headers()->get('X-Seeders-Model-Type'),
                'trackable_id' => $request->headers()->get('X-Seeders-Model-Id'),
                'scope' => $request->headers()->get('X-Seeders-Scope'),
                'integration' => $this->getIntegrationName($connector),
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'consumption' => $this->extractConsumption($response)['value'],
                'consumption_type' => $this->extractConsumption($response)['type'],
                'latency_ms' => $this->getLatencyMs($response),
                'metadata' => $this->extractMetadata($response),
            ]);
        } catch (Throwable $e) {
            Log::warning('Failed to record API usage: '.$e->getMessage());
            report($e);
        }
    }

    protected function extractConsumption(Response $response): array
    {
        if ($units = $response->header('x-api-units-cost-total-actual')) {
            return ['value' => (float) $units, 'type' => 'units'];
        }

        if ($cost = $response->json('cost')) {
            return ['value' => (float) $cost, 'type' => 'dollars'];
        }

        if ($tokens = $response->json('usage.total_tokens')) {
            return ['value' => (float) $tokens, 'type' => 'tokens'];
        }

        return ['value' => 1, 'type' => 'requests'];
    }

    protected function extractMetadata(Response $response): ?array
    {
        $metadata = [];

        if ($usage = $response->json('usage')) {
            $metadata['usage'] = $usage;
        }

        if ($unitsActual = $response->header('x-api-units-cost-total-actual')) {
            $metadata['units'] = [
                'actual' => (int) $unitsActual,
                'limit_reset' => $response->header('x-api-units-limit-reset'),
            ];
        }

        if (! $response->successful()) {
            $metadata['error'] = $response->json('error') ?? $response->body();
        }

        return empty($metadata) ? null : $metadata;
    }

    protected function getLatencyMs(Response $response): ?int
    {
        $stats = $response->getPsrResponse()->getHeader('X-Response-Time');

        if (! empty($stats)) {
            return (int) ($stats[0] * 1000);
        }

        return null;
    }

    protected function getIntegrationName($connector): string
    {
        if (method_exists($connector, 'getIntegrationName')) {
            return $connector->getIntegrationName();
        }

        $className = class_basename($connector);

        return strtolower(str_replace('Connector', '', $className));
    }
}
