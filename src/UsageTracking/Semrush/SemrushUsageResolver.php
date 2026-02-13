<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Semrush;

use RuntimeException;
use Saloon\Http\Request;
use Seeders\ExternalApis\Integrations\Semrush\Requests\ApiUnitsBalanceRequest;
use Seeders\ExternalApis\Integrations\Semrush\Requests\BacklinksOverviewRequest;
use Seeders\ExternalApis\Integrations\Semrush\Requests\BatchComparisonRequest;

class SemrushUsageResolver
{
    public function assertSupported(Request $request): void
    {
        $this->resolve($request);
    }

    /**
     * @return array{
     *     request_type: string,
     *     feature: string,
     *     units: int,
     *     target_count: int|null
     * }
     */
    public function resolve(Request $request): array
    {
        $requestType = $this->resolveRequestType($request);
        $targetCount = $this->resolveTargetCount($requestType, $request);

        return [
            'request_type' => $requestType,
            'feature' => $this->resolveFeature($requestType),
            'units' => $this->resolveUnits($requestType, $targetCount),
            'target_count' => $targetCount,
        ];
    }

    private function resolveRequestType(Request $request): string
    {
        return match (true) {
            $request instanceof BacklinksOverviewRequest => 'backlinks_overview',
            $request instanceof BatchComparisonRequest => 'backlinks_comparison',
            $request instanceof ApiUnitsBalanceRequest => 'api_units',
            default => throw new RuntimeException(sprintf(
                'Unsupported Semrush request class [%s]. Add unit mapping before using this request.',
                $request::class
            )),
        };
    }

    private function resolveFeature(string $requestType): string
    {
        return match ($requestType) {
            'backlinks_overview' => 'backlinks_overview',
            'backlinks_comparison' => 'backlinks_comparison',
            'api_units' => 'api_units_balance',
            default => throw new RuntimeException("Unsupported Semrush request type [{$requestType}]."),
        };
    }

    private function resolveTargetCount(string $requestType, Request $request): ?int
    {
        if ($requestType !== 'backlinks_comparison') {
            return null;
        }

        if (! $request instanceof BatchComparisonRequest) {
            throw new RuntimeException('Invalid request instance for backlinks_comparison.');
        }

        return count($request->targets);
    }

    private function resolveUnits(string $requestType, ?int $targetCount): int
    {
        return match ($requestType) {
            'backlinks_overview' => $this->getUnitRule('backlinks_overview'),
            'backlinks_comparison' => $this->getUnitRule('backlinks_comparison_per_target') * (int) $targetCount,
            'api_units' => $this->getUnitRule('api_units'),
            default => throw new RuntimeException("Unsupported Semrush request type [{$requestType}]."),
        };
    }

    private function getUnitRule(string $key): int
    {
        $value = config("external-apis.usage_tracking.semrush.unit_rules.{$key}");

        if (! is_int($value)) {
            throw new RuntimeException(
                "Missing Semrush unit rule for [{$key}] in external-apis.usage_tracking.semrush.unit_rules."
            );
        }

        return $value;
    }
}
