<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Tracks general API consumption (Ahrefs, SEMrush, DataForSEO, etc.).
 *
 * @property int $id
 * @property string $integration
 * @property string|null $scope
 * @property string|null $trackable_type
 * @property int|null $trackable_id
 * @property string|null $endpoint
 * @property int|null $status
 * @property float $consumption
 * @property array|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ApiConsumptionLog extends Model
{
    protected $table = 'api_logs';

    protected $guarded = ['id'];

    protected $casts = [
        'consumption' => 'decimal:6',
        'metadata' => 'json',
    ];

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    #[Scope]
    protected function forIntegration(Builder $query, string $integration): Builder
    {
        return $query->where('integration', $integration);
    }

    #[Scope]
    protected function forScope(Builder $query, string $scope): Builder
    {
        return $query->where('scope', $scope);
    }

    #[Scope]
    protected function today(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    #[Scope]
    protected function thisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    #[Scope]
    protected function successful(Builder $query): Builder
    {
        return $query->where('status', '>=', 200)
            ->where('status', '<', 300);
    }

    #[Scope]
    protected function failed(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->where('status', '<', 200)
                ->orWhere('status', '>=', 400);
        });
    }
}
