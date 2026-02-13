<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;

class ApiBudgetConfig extends Model
{
    protected $table = 'api_budget_config';

    protected $fillable = [
        'integration',
        'monthly_budget',
        'daily_budget',
        'warning_threshold',
        'critical_threshold',
        'alert_enabled',
        'google_chat_webhook_url',
        'is_active',
    ];

    protected $casts = [
        'monthly_budget' => 'float',
        'daily_budget' => 'float',
        'warning_threshold' => 'integer',
        'critical_threshold' => 'integer',
        'alert_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    #[Scope]
    protected function active($query)
    {
        return $query->where('is_active', true);
    }

    public static function getOpenAIBudget(): ?self
    {
        return self::where('integration', 'openai')->first();
    }

    public function shouldAlert(): bool
    {
        return $this->alert_enabled && $this->is_active;
    }
}
