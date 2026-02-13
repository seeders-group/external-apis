<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Seeders\ExternalApis\UsageTracking\Models\ApiBudgetConfig;

beforeEach(function (): void {
    Schema::dropIfExists('api_budget_config');

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

it('can create a budget config', function (): void {
    $config = ApiBudgetConfig::create([
        'integration' => 'openai',
        'monthly_budget' => 500.00,
        'warning_threshold' => 80,
        'critical_threshold' => 90,
        'alert_enabled' => true,
        'is_active' => true,
    ]);

    expect($config->integration)->toBe('openai');
    expect($config->monthly_budget)->toBe(500.0);
    expect($config->warning_threshold)->toBe(80);
    expect($config->critical_threshold)->toBe(90);
});

it('retrieves openai budget config', function (): void {
    ApiBudgetConfig::create([
        'integration' => 'openai',
        'monthly_budget' => 500.00,
    ]);

    $budget = ApiBudgetConfig::getOpenAIBudget();

    expect($budget)->not->toBeNull();
    expect($budget->integration)->toBe('openai');
});

it('returns null when no openai budget exists', function (): void {
    expect(ApiBudgetConfig::getOpenAIBudget())->toBeNull();
});

it('determines if alerts should be sent', function (): void {
    $activeAlerts = ApiBudgetConfig::create([
        'integration' => 'openai',
        'monthly_budget' => 500.00,
        'alert_enabled' => true,
        'is_active' => true,
    ]);

    $disabledAlerts = ApiBudgetConfig::create([
        'integration' => 'semrush',
        'monthly_budget' => 100.00,
        'alert_enabled' => false,
        'is_active' => true,
    ]);

    $inactiveConfig = ApiBudgetConfig::create([
        'integration' => 'ahrefs',
        'monthly_budget' => 200.00,
        'alert_enabled' => true,
        'is_active' => false,
    ]);

    expect($activeAlerts->shouldAlert())->toBeTrue();
    expect($disabledAlerts->shouldAlert())->toBeFalse();
    expect($inactiveConfig->shouldAlert())->toBeFalse();
});

it('scopes active configs', function (): void {
    ApiBudgetConfig::create(['integration' => 'openai', 'monthly_budget' => 500, 'is_active' => true]);
    ApiBudgetConfig::create(['integration' => 'semrush', 'monthly_budget' => 100, 'is_active' => false]);

    expect(ApiBudgetConfig::active()->count())->toBe(1);
});
