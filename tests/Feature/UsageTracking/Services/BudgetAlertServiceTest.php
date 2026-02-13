<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Seeders\ExternalApis\UsageTracking\Models\ApiBudgetConfig;
use Seeders\ExternalApis\UsageTracking\Models\ApiUsageLog;
use Seeders\ExternalApis\UsageTracking\Services\BudgetAlertService;

beforeEach(function (): void {
    Schema::dropIfExists('api_usage_logs');
    Schema::dropIfExists('api_budget_config');

    Schema::create('api_usage_logs', function (Blueprint $table): void {
        $table->id();
        $table->string('integration', 50)->index();
        $table->string('request_id')->nullable();
        $table->string('model', 50)->nullable();
        $table->string('endpoint', 100)->nullable();
        $table->integer('prompt_tokens')->nullable();
        $table->integer('completion_tokens')->nullable();
        $table->integer('total_tokens')->nullable();
        $table->integer('input_cached_tokens')->nullable();
        $table->integer('images_generated')->nullable();
        $table->integer('characters_processed')->nullable();
        $table->integer('seconds_processed')->nullable();
        $table->decimal('estimated_cost', 10, 6);
        $table->decimal('actual_cost', 10, 6)->nullable();
        $table->string('feature', 100)->index();
        $table->string('sub_feature', 100)->nullable();
        $table->unsignedBigInteger('project_id')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('status', 20)->default('success');
        $table->text('error_message')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamp('reconciled_at')->nullable();
        $table->timestamps();
    });

    Schema::create('api_budget_config', function (Blueprint $table): void {
        $table->id();
        $table->string('integration');
        $table->decimal('monthly_budget', 10, 2)->nullable();
        $table->decimal('daily_budget', 10, 2)->nullable();
        $table->integer('warning_threshold')->default(80);
        $table->integer('critical_threshold')->default(90);
        $table->boolean('alert_enabled')->default(true);
        $table->string('google_chat_webhook_url')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
});

it('calculates threshold amounts correctly', function (): void {
    $service = new BudgetAlertService;

    expect($service->calculateThresholdAmount(1000.0, 80))->toBe(800.0);
    expect($service->calculateThresholdAmount(500.0, 90))->toBe(450.0);
    expect($service->calculateThresholdAmount(100.0, 50))->toBe(50.0);
});

it('returns no budget status when no config exists', function (): void {
    $service = new BudgetAlertService;

    $status = $service->checkBudgetStatus('openai');

    expect($status['status'])->toBe('no_budget');
});

it('returns ok status when under warning threshold', function (): void {
    ApiBudgetConfig::create([
        'integration' => 'openai',
        'monthly_budget' => 1000.00,
        'warning_threshold' => 80,
        'critical_threshold' => 90,
        'alert_enabled' => true,
        'is_active' => true,
    ]);

    ApiUsageLog::create([
        'integration' => 'openai',
        'estimated_cost' => 100.0,
        'feature' => 'test',
        'status' => 'success',
    ]);

    $service = new BudgetAlertService;
    $status = $service->checkBudgetStatus('openai');

    expect($status['status'])->toBe('ok');
    expect($status['current_spend'])->toBe(100.0);
    expect($status['monthly_budget'])->toBe(1000.0);
});

it('returns warning status when over warning threshold', function (): void {
    ApiBudgetConfig::create([
        'integration' => 'openai',
        'monthly_budget' => 100.00,
        'warning_threshold' => 80,
        'critical_threshold' => 90,
        'alert_enabled' => true,
        'is_active' => true,
    ]);

    ApiUsageLog::create([
        'integration' => 'openai',
        'estimated_cost' => 85.0,
        'feature' => 'test',
        'status' => 'success',
    ]);

    $service = new BudgetAlertService;
    $status = $service->checkBudgetStatus('openai');

    expect($status['status'])->toBe('warning');
    expect($status['thresholds']['warning']['exceeded'])->toBeTrue();
    expect($status['thresholds']['critical']['exceeded'])->toBeFalse();
});

it('returns critical status when over critical threshold', function (): void {
    ApiBudgetConfig::create([
        'integration' => 'openai',
        'monthly_budget' => 100.00,
        'warning_threshold' => 80,
        'critical_threshold' => 90,
        'alert_enabled' => true,
        'is_active' => true,
    ]);

    ApiUsageLog::create([
        'integration' => 'openai',
        'estimated_cost' => 95.0,
        'feature' => 'test',
        'status' => 'success',
    ]);

    $service = new BudgetAlertService;
    $status = $service->checkBudgetStatus('openai');

    expect($status['status'])->toBe('critical');
    expect($status['thresholds']['warning']['exceeded'])->toBeTrue();
    expect($status['thresholds']['critical']['exceeded'])->toBeTrue();
});

it('uses units for semrush budget calculations', function (): void {
    ApiBudgetConfig::create([
        'integration' => 'semrush',
        'monthly_budget' => 10000,
        'warning_threshold' => 80,
        'critical_threshold' => 90,
        'alert_enabled' => true,
        'is_active' => true,
    ]);

    ApiUsageLog::create([
        'integration' => 'semrush',
        'estimated_cost' => 0.002,
        'total_tokens' => 40,
        'feature' => 'backlinks',
        'status' => 'success',
    ]);

    $service = new BudgetAlertService;
    $status = $service->checkBudgetStatus('semrush');

    // Should use total_tokens (units) not estimated_cost
    expect($status['current_spend'])->toBe(40.0);
});
