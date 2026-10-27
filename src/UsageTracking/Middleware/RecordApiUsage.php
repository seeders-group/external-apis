<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Middleware;

use Illuminate\Support\Facades\Log;
use Saloon\Http\Response;
use Seeders\ExternalApis\UsageTracking\UsageTracking;
use Throwable;

class RecordApiUsage
{
    /**
     * Error bodies are often whole HTML pages; the start is enough to diagnose the failure.
     */
    protected const ERROR_BODY_LIMIT = 2000;

    public function __invoke(Response $response): void
    {
        if (! config('external-apis.usage_tracking.enabled', true)) {
            return;
        }

        try {
            $request = $response->getPendingRequest();
            $connector = $request->getConnector();

            $saloonRequest = $request->getRequest();
            $endpoint = $saloonRequest->resolveEndpoint();

            $apiLogModel = UsageTracking::$apiLogModel;
            $consumption = $this->extractConsumption($response);

            $apiLogModel::create([
                'trackable_type' => $request->headers()->get('X-Seeders-Model-Type'),
                'trackable_id' => $request->headers()->get('X-Seeders-Model-Id'),
                'scope' => $request->headers()->get('X-Seeders-Scope'),
                'integration' => $this->getIntegrationName($connector),
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'consumption' => $consumption['value'],
                'consumption_type' => $consumption['type'],
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
        $pendingRequest = $response->getPendingRequest();
        $expectedConsumption = $pendingRequest->headers()->get('X-Seeders-Expected-Consumption');
        $expectedConsumptionType = $pendingRequest->headers()->get('X-Seeders-Expected-Consumption-Type');

        if (! is_null($expectedConsumption) && is_numeric($expectedConsumption)) {
            return [
                'value' => $response->successful() ? (float) $expectedConsumption : 0.0,
                'type' => $expectedConsumptionType ?: 'units',
            ];
        }

        $units = $response->header('x-api-units-cost-total-actual');
        if (! is_null($units) && $units !== '') {
            return ['value' => (float) $units, 'type' => 'units'];
        }

        $cost = $this->safeJson($response, 'cost');
        if (! is_null($cost) && is_numeric($cost)) {
            return ['value' => (float) $cost, 'type' => 'dollars'];
        }

        $tokens = $this->safeJson($response, 'usage.total_tokens');
        if (! is_null($tokens) && is_numeric($tokens)) {
            return ['value' => (float) $tokens, 'type' => 'tokens'];
        }

        return ['value' => 1, 'type' => 'requests'];
    }

    protected function extractMetadata(Response $response): ?array
    {
        $metadata = [];

        if ($usage = $this->safeJson($response, 'usage')) {
            $metadata['usage'] = $usage;
        }

        $unitsActual = $response->header('x-api-units-cost-total-actual');
        if (! is_null($unitsActual) && $unitsActual !== '') {
            $metadata['units'] = [
                'actual' => (int) $unitsActual,
                'limit_reset' => $response->header('x-api-units-limit-reset'),
            ];
        }

        if (! $response->successful()) {
            $error = $this->safeJson($response, 'error') ?? $response->body();

            $metadata['error'] = is_string($error) ? mb_substr($error, 0, self::ERROR_BODY_LIMIT) : $error;
        }

        return $metadata === [] ? null : $this->sanitize($metadata);
    }

    /**
     * Make metadata storable: PostgreSQL jsonb rejects \u0000 and json_encode rejects
     * invalid UTF-8, and either one in a response body would lose the whole log row.
     */
    protected function sanitize(mixed $value): mixed
    {
        if (is_string($value)) {
            return str_replace("\0", '', mb_scrub($value, 'UTF-8'));
        }

        if (! is_array($value)) {
            return $value;
        }

        $sanitized = [];

        foreach ($value as $key => $item) {
            $sanitized[is_string($key) ? $this->sanitize($key) : $key] = $this->sanitize($item);
        }

        return $sanitized;
    }

    protected function safeJson(Response $response, ?string $key = null): mixed
    {
        try {
            return $response->json($key);
        } catch (Throwable) {
            return null;
        }
    }

    protected function getLatencyMs(Response $response): ?int
    {
        $stats = $response->getPsrResponse()->getHeader('X-Response-Time');

        if ($stats !== []) {
            return (int) ((float) $stats[0] * 1000);
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
