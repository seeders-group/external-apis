<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pricing configuration for credit/unit-based API services (Ahrefs, DataForSEO, etc.)
 * Different from AiModelPricing which is token-based (OpenAI, Gemini)
 */
class ApiServicePricing extends Model
{
    protected $table = 'api_service_pricing';

    protected $fillable = [
        'integration',
        'endpoint',
        'cost_per_unit',
        'unit_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'cost_per_unit' => 'decimal:6',
        'is_active' => 'boolean',
    ];

    /**
     * Get pricing for a specific integration and endpoint.
     */
    public static function getPricing(string $integration, ?string $endpoint = null): ?array
    {
        if ($endpoint) {
            $pricing = self::where('integration', $integration)
                ->where('endpoint', $endpoint)
                ->where('is_active', true)
                ->first();

            if ($pricing) {
                return [
                    'cost_per_unit' => (float) $pricing->cost_per_unit,
                    'unit_type' => $pricing->unit_type,
                ];
            }
        }

        $defaultPricing = self::where('integration', $integration)
            ->whereNull('endpoint')
            ->where('is_active', true)
            ->first();

        if ($defaultPricing) {
            return [
                'cost_per_unit' => (float) $defaultPricing->cost_per_unit,
                'unit_type' => $defaultPricing->unit_type,
            ];
        }

        // Fallback to config (try package config first, then app config)
        $configPricing = config("external-apis.usage_tracking.pricing.{$integration}")
            ?? config("api_pricing.{$integration}");

        if ($configPricing && isset($configPricing['cost_per_unit'])) {
            return [
                'cost_per_unit' => (float) $configPricing['cost_per_unit'],
                'unit_type' => $configPricing['unit_type'] ?? 'api_units',
            ];
        }

        return null;
    }

    /**
     * Calculate cost from units consumed.
     */
    public static function calculateCost(string $integration, int $unitsConsumed, ?string $endpoint = null): float
    {
        $pricing = self::getPricing($integration, $endpoint);

        if (! $pricing) {
            return 0.0;
        }

        return round($unitsConsumed * $pricing['cost_per_unit'], 6);
    }
}
