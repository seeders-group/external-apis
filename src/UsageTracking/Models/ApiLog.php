<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
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

    #[Scope]
    protected function forIntegration($query, string $integration)
    {
        return $query->where('integration', $integration);
    }

    #[Scope]
    protected function forScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    #[Scope]
    protected function today($query)
    {
        return $query->whereDate('created_at', today());
    }

    #[Scope]
    protected function thisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    #[Scope]
    protected function successful($query)
    {
        return $query->where('status', '>=', 200)
            ->where('status', '<', 300);
    }

    #[Scope]
    protected function failed($query)
    {
        return $query->where(function ($q): void {
            $q->where('status', '<', 200)
                ->orWhere('status', '>=', 400);
        });
    }
}
