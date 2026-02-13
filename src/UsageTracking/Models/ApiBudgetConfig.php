<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $integration
 * @property float $monthly_budget
 * @property float|null $daily_budget
 * @property int $warning_threshold
 * @property int $critical_threshold
 * @property bool $alert_enabled
 * @property string|null $google_chat_webhook_url
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
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
