<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

/**
 * Tracks AI / LLM token-based API usage (OpenAI, Gemini, etc.).
 *
 * @property int $id
 * @property string $integration
 * @property string|null $request_id
 * @property string|null $model
 * @property string|null $endpoint
 * @property int $prompt_tokens
 * @property int $completion_tokens
 * @property int $total_tokens
 * @property int $input_cached_tokens
 * @property int $images_generated
 * @property int $characters_processed
 * @property int $seconds_processed
 * @property string|null $feature
 * @property string|null $sub_feature
 * @property int|null $project_id
 * @property int|null $user_id
 * @property string|null $trackable_type
 * @property int|null $trackable_id
 * @property string $status
 * @property string|null $error_message
 * @property array|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AiUsageLog extends Model
{
    protected $table = 'api_usage_logs';

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
        'feature',
        'sub_feature',
        'project_id',
        'user_id',
        'trackable_type',
        'trackable_id',
        'status',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
        'input_cached_tokens' => 'integer',
        'images_generated' => 'integer',
        'characters_processed' => 'integer',
        'seconds_processed' => 'integer',
        'metadata' => 'array',
    ];

    #[Scope]
    protected function today(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    #[Scope]
    protected function thisMonth(Builder $query): Builder
    {
        return $query->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month);
    }

    #[Scope]
    protected function byIntegration(Builder $query, string $integration): Builder
    {
        return $query->where('integration', $integration);
    }

    #[Scope]
    protected function byFeature(Builder $query, string $feature): Builder
    {
        return $query->where('feature', $feature);
    }

    #[Scope]
    protected function byModel(Builder $query, string $model): Builder
    {
        return $query->where('model', $model);
    }

    #[Scope]
    protected function successful(Builder $query): Builder
    {
        return $query->where('status', 'success');
    }

    #[Scope]
    protected function failed(Builder $query): Builder
    {
        return $query->where('status', 'error');
    }

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UsageTracking::$userModel);
    }
}
