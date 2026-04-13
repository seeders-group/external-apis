<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Models;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month);
    }

    public function scopeByIntegration($query, string $integration)
    {
        return $query->where('integration', $integration);
    }

    public function scopeByFeature($query, string $feature)
    {
        return $query->where('feature', $feature);
    }

    public function scopeByModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
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
