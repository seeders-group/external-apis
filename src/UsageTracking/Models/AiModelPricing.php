<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Models;

use Illuminate\Database\Eloquent\Model;

class AiModelPricing extends Model
{
    protected $table = 'ai_model_pricing';

    protected $fillable = [
        'integration',
        'model',
        'input_per_1m_tokens',
        'output_per_1m_tokens',
        'cached_input_per_1m_tokens',
    ];

    protected $casts = [
        'input_per_1m_tokens' => 'decimal:6',
        'output_per_1m_tokens' => 'decimal:6',
        'cached_input_per_1m_tokens' => 'decimal:6',
    ];

    /**
     * Get pricing for a specific model, with fallback to config.
     */
    public static function getPricing(string $model, string $integration = 'openai'): ?array
    {
        $pricing = self::where('integration', $integration)
            ->where('model', $model)
            ->first();

        if ($pricing) {
            return [
                'input_per_1m_tokens' => (float) $pricing->input_per_1m_tokens,
                'output_per_1m_tokens' => (float) $pricing->output_per_1m_tokens,
                'cached_input_per_1m_tokens' => $pricing->cached_input_per_1m_tokens
                    ? (float) $pricing->cached_input_per_1m_tokens
                    : null,
            ];
        }

        // Fallback to config (try package config first, then app config)
        return config("external-apis.usage_tracking.pricing.{$integration}.models.{$model}")
            ?? config("ai_pricing.{$integration}.models.{$model}");
    }

    /**
     * Get all pricing for an integration, merged with config defaults.
     */
    public static function getAllPricing(string $integration = 'openai'): array
    {
        $configPricing = collect(
            config("external-apis.usage_tracking.pricing.{$integration}.models",
                config("ai_pricing.{$integration}.models", []))
        )->filter(fn ($pricing) => isset($pricing['input_per_1m_tokens']));

        $dbPricing = self::where('integration', $integration)
            ->get()
            ->keyBy('model')
            ->map(fn ($pricing) => [
                'input_per_1m_tokens' => (float) $pricing->input_per_1m_tokens,
                'output_per_1m_tokens' => (float) $pricing->output_per_1m_tokens,
                'cached_input_per_1m_tokens' => $pricing->cached_input_per_1m_tokens
                    ? (float) $pricing->cached_input_per_1m_tokens
                    : null,
            ]);

        return $configPricing->merge($dbPricing)->toArray();
    }
}
