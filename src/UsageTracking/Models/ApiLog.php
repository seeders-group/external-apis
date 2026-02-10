<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ApiLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'consumption' => 'decimal:6',
        'metadata' => 'json',
    ];

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForIntegration($query, string $integration)
    {
        return $query->where('integration', $integration);
    }

    public function scopeForScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', '>=', 200)
            ->where('status', '<', 300);
    }

    public function scopeFailed($query)
    {
        return $query->where(function ($q) {
            $q->where('status', '<', 200)
                ->orWhere('status', '>=', 400);
        });
    }
}
