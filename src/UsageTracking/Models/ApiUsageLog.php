<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

class ApiUsageLog extends Model
{
    protected $fillable = [
        'integration',
        'request_id',
        'model',
        'endpoint',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'input_cached_tokens',
        'images_generated',
        'characters_processed',
        'seconds_processed',
        'estimated_cost',
        'actual_cost',
        'feature',
        'sub_feature',
        'project_id',
        'user_id',
        'status',
        'error_message',
        'metadata',
        'reconciled_at',
    ];

    protected $casts = [
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
        'input_cached_tokens' => 'integer',
        'images_generated' => 'integer',
        'characters_processed' => 'integer',
        'seconds_processed' => 'integer',
        'estimated_cost' => 'decimal:6',
        'actual_cost' => 'decimal:6',
        'metadata' => 'array',
        'reconciled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    #[Scope]
    protected function today($query)
    {
        return $query->whereDate('created_at', today());
    }

    #[Scope]
    protected function thisMonth($query)
    {
        return $query->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month);
    }

    #[Scope]
    protected function byIntegration($query, string $integration)
    {
        return $query->where('integration', $integration);
    }

    #[Scope]
    protected function byFeature($query, string $feature)
    {
        return $query->where('feature', $feature);
    }

    #[Scope]
    protected function byModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    #[Scope]
    protected function successful($query)
    {
        return $query->where('status', 'success');
    }

    #[Scope]
    protected function failed($query)
    {
        return $query->where('status', 'error');
    }

    #[Scope]
    protected function reconciled($query)
    {
        return $query->whereNotNull('reconciled_at');
    }

    #[Scope]
    protected function unreconciled($query)
    {
        return $query->whereNull('reconciled_at');
    }

    protected function getCostInDollarsAttribute(): string
    {
        return '$'.number_format($this->estimated_cost, 4);
    }

    protected function getIsReconciledAttribute(): bool
    {
        return $this->reconciled_at !== null;
    }

    /**
     * Calculate cost from aggregated token counts.
     * This avoids precision loss from summing many small individual costs.
     */
    public static function calculateCostFromTokens(
        string $model,
        int $totalPromptTokens,
        int $totalCompletionTokens,
        int $totalCachedTokens = 0
    ): float {
        $pricingModel = UsageTracking::$aiModelPricingModel;
        $pricing = $pricingModel::getPricing($model, 'openai');

        if (! $pricing) {
            return 0.0;
        }

        $regularInputTokens = $totalPromptTokens - $totalCachedTokens;
        $inputCost = ($regularInputTokens / 1_000_000) * $pricing['input_per_1m_tokens'];

        $cachedCost = 0;
        if ($totalCachedTokens > 0 && isset($pricing['cached_input_per_1m_tokens']) && $pricing['cached_input_per_1m_tokens']) {
            $cachedCost = ($totalCachedTokens / 1_000_000) * $pricing['cached_input_per_1m_tokens'];
        }

        $outputCost = ($totalCompletionTokens / 1_000_000) * $pricing['output_per_1m_tokens'];

        return round($inputCost + $cachedCost + $outputCost, 6);
    }

    /**
     * Calculate total cost from a collection of logs by aggregating tokens per model.
     * This avoids precision loss from summing many small individual costs.
     *
     * @param  Collection|\Illuminate\Database\Eloquent\Collection  $logs
     */
    public static function calculateTotalCostFromLogs($logs): float
    {
        $modelGroups = $logs->groupBy('model');

        $totalCost = 0.0;

        foreach ($modelGroups as $model => $modelLogs) {
            $totalPromptTokens = $modelLogs->sum('prompt_tokens');
            $totalCompletionTokens = $modelLogs->sum('completion_tokens');
            $totalCachedTokens = $modelLogs->sum('input_cached_tokens');

            $cost = self::calculateCostFromTokens(
                $model,
                (int) $totalPromptTokens,
                (int) $totalCompletionTokens,
                (int) $totalCachedTokens
            );

            $totalCost += $cost;
        }

        return $totalCost;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UsageTracking::$userModel);
    }
}
