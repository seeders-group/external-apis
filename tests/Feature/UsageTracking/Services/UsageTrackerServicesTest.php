<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Prism\Prism\Enums\Provider;
use Seeders\ExternalApis\UsageTracking\Models\AiModelPricing;
use Seeders\ExternalApis\UsageTracking\Models\ApiBudgetConfig;
use Seeders\ExternalApis\UsageTracking\Models\ApiServicePricing;
use Seeders\ExternalApis\UsageTracking\Models\ApiUsageLog;
use Seeders\ExternalApis\UsageTracking\Services\AhrefsUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\BudgetAlertService;
use Seeders\ExternalApis\UsageTracking\Services\DataForSeoUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\OpenAIUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\PrismUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\SemrushUsageTrackerService;

beforeEach(function (): void {
    Schema::dropIfExists('api_usage_logs');
    Schema::dropIfExists('api_service_pricing');
    Schema::dropIfExists('api_budget_config');
    Schema::dropIfExists('ai_model_pricing');

    Schema::create('api_usage_logs', function (Blueprint $table): void {
        $table->id();
        $table->string('integration', 50)->index();
        $table->string('request_id')->nullable();
        $table->string('model', 50)->nullable()->index();
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

    Schema::create('api_service_pricing', function (Blueprint $table): void {
        $table->id();
        $table->string('integration');
        $table->string('endpoint')->nullable();
        $table->decimal('cost_per_unit', 10, 6)->default(0);
        $table->string('unit_type')->default('api_units');
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
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

    Schema::create('ai_model_pricing', function (Blueprint $table): void {
        $table->id();
        $table->string('integration');
        $table->string('model');
        $table->decimal('input_per_1m_tokens', 10, 6);
        $table->decimal('output_per_1m_tokens', 10, 6);
        $table->decimal('cached_input_per_1m_tokens', 10, 6)->nullable();
        $table->timestamps();
    });
});

afterEach(function (): void {
    \Mockery::close();
});

it('tracks openai requests, errors, and budget thresholds', function (): void {
    AiModelPricing::create([
        'integration' => 'openai',
        'model' => 'gpt-test',
        'input_per_1m_tokens' => 2.0,
        'output_per_1m_tokens' => 4.0,
        'cached_input_per_1m_tokens' => 1.0,
    ]);

    ApiBudgetConfig::create([
        'integration' => 'openai',
        'monthly_budget' => 10.0,
        'warning_threshold' => 80,
        'critical_threshold' => 90,
        'is_active' => true,
        'alert_enabled' => true,
    ]);

    config()->set('external-apis.usage_tracking.pricing.openai.models.dall-e-test', [
        'hd_1024x1024' => 0.08,
    ]);

    $service = new OpenAIUsageTrackerService;
    $usage = $service->logRequest('gpt-test', 1_000_000, 500_000, ['feature' => 'chat'], 250_000, 'req-1');
    $image = $service->logImageGeneration('dall-e-test', 2, '1024x1024', 'hd', ['feature' => 'images']);
    $error = $service->logError('gpt-test', 'network timeout', ['feature' => 'chat']);

    expect((float) $usage->estimated_cost)->toBe(3.75);
    expect((float) $image->estimated_cost)->toBe(0.16);
    expect($error->status)->toBe('error');

    expect($service->calculateCost('missing-model', 100, 100))->toBe(0.0);
    expect($service->getTodaySpend())->toBeGreaterThan(3.8);

    ApiUsageLog::query()->update(['actual_cost' => 9.5]);
    $threshold = $service->checkBudgetThreshold();

    expect($threshold['status'])->toBe('critical');
});

it('tracks ahrefs usage and logs warning when budget check fails', function (): void {
    ApiServicePricing::create([
        'integration' => 'ahrefs',
        'endpoint' => '/site-explorer/backlinks',
        'cost_per_unit' => 0.0001,
        'is_active' => true,
    ]);

    $budgetAlertMock = \Mockery::mock(BudgetAlertService::class);
    $budgetAlertMock->shouldReceive('checkAndAlert')->once()->andThrow(new \RuntimeException('boom'));
    app()->instance(BudgetAlertService::class, $budgetAlertMock);

    Log::shouldReceive('warning')
        ->once()
        ->withArgs(fn (string $message): bool => str_contains($message, 'Failed to check Ahrefs budget threshold'));

    $service = new AhrefsUsageTrackerService;
    $log = $service->logRequest('/site-explorer/backlinks', 40, ['feature' => 'seo']);
    $error = $service->logError('/site-explorer/backlinks', 'rate limit');

    expect((float) $log->estimated_cost)->toBe(0.004);
    expect($error->status)->toBe('error');
    expect($service->getTodayUnitsConsumed())->toBe(40);
    expect($service->getMonthToDateSpend())->toBe(0.004);
});

it('tracks dataforseo usage with endpoint-derived features', function (): void {
    $budgetAlertMock = \Mockery::mock(BudgetAlertService::class);
    $budgetAlertMock->shouldReceive('checkAndAlert')->twice()->with('dataforseo');
    app()->instance(BudgetAlertService::class, $budgetAlertMock);

    $service = new DataForSeoUsageTrackerService;
    $service->logRequest('/v3/business_data/google/reviews/task_get/advanced', 0.15);
    $service->logRequest('/v3/serp/google/organic/live/advanced', 0.20);
    $error = $service->logError('/v3/merchant/google/products/task_get', 'api error');

    $byFeature = $service->getMonthSpendByFeature();
    $features = array_column($byFeature, 'feature');

    expect($error->feature)->toBe('merchant');
    expect($service->getTodayRequests())->toBe(3);
    expect($service->getMonthToDateSpend())->toBe(0.35);
    expect($features)->toContain('reviews');
    expect($features)->toContain('serp');
});

it('tracks prism usage across text, embeddings, image, and audio paths', function (): void {
    AiModelPricing::create([
        'integration' => 'openai',
        'model' => 'gpt-prism',
        'input_per_1m_tokens' => 1.0,
        'output_per_1m_tokens' => 2.0,
        'cached_input_per_1m_tokens' => 0.5,
    ]);

    ApiBudgetConfig::create([
        'integration' => 'openai',
        'monthly_budget' => 2.0,
        'warning_threshold' => 50,
        'critical_threshold' => 75,
        'is_active' => true,
        'alert_enabled' => true,
    ]);

    config()->set('external-apis.usage_tracking.pricing.openai.embeddings.embed-test', 0.2);
    config()->set('external-apis.usage_tracking.pricing.openai.models.image-test', [
        'default' => 0.05,
        '1024x1024' => 0.08,
    ]);
    config()->set('external-apis.usage_tracking.pricing.openai.audio.tts-test', [
        'per_1m_characters' => 3.0,
        'per_minute' => 0.12,
    ]);

    $service = new PrismUsageTrackerService;

    $text = $service->logRequest(
        Provider::OpenAI,
        'gpt-prism',
        200_000,
        100_000,
        ['feature' => 'assistant'],
        50_000,
        100,
        'req-prism',
        'structured'
    );

    $embedding = $service->logEmbeddingsRequest(
        Provider::OpenAI,
        'embed-test',
        500_000,
        ['feature' => 'embeddings'],
        'req-embed'
    );

    $image = $service->logImageGeneration(
        Provider::OpenAI,
        'image-test',
        2,
        ['feature' => 'images'],
        '1024x1024',
        null
    );

    $audio = $service->logAudioRequest(
        Provider::OpenAI,
        'tts-test',
        ['feature' => 'audio'],
        300_000,
        null,
        'req-audio'
    );

    $error = $service->logError(Provider::OpenAI, 'gpt-prism', 'bad response', ['feature' => 'assistant']);

    ApiUsageLog::query()->where('id', $text->id)->update(['actual_cost' => 1.6]);
    $budget = $service->checkBudgetThreshold(Provider::OpenAI);

    expect($text->integration)->toBe('openai');
    expect($text->metadata['thought_tokens'])->toBe(100);
    expect((float) $embedding->estimated_cost)->toBe(0.1);
    expect((float) $image->estimated_cost)->toBe(0.16);
    expect((float) $audio->estimated_cost)->toBe(0.9);
    expect($error->status)->toBe('error');
    expect($service->providerToIntegration(Provider::OpenAI))->toBe('openai');
    expect($service->getTodaySpend(Provider::OpenAI))->toBeGreaterThan(1.2);
    expect($service->getMonthToDateSpend(Provider::OpenAI))->toBe(1.6);
    expect($service->getTotalPrismSpendToday())->toBeGreaterThan(1.2);
    expect($budget['status'])->toBe('critical');
});

it('returns no-budget for prism provider without config', function (): void {
    $service = new PrismUsageTrackerService;

    $status = $service->checkBudgetThreshold(Provider::Gemini);

    expect($status['status'])->toBe('no_budget');
});

it('handles openai budget status branches and image fallback pricing', function (): void {
    $service = new OpenAIUsageTrackerService;

    expect($service->checkBudgetThreshold()['status'])->toBe('no_budget');
    expect($service->calculateImageCost('missing-model', '1024x1024', 'standard', 1))->toBe(0.0);

    ApiBudgetConfig::create([
        'integration' => 'openai',
        'monthly_budget' => 100.0,
        'warning_threshold' => 80,
        'critical_threshold' => 90,
        'is_active' => true,
        'alert_enabled' => true,
    ]);

    ApiUsageLog::create([
        'integration' => 'openai',
        'estimated_cost' => 10.0,
        'actual_cost' => 10.0,
        'feature' => 'chat',
        'status' => 'success',
    ]);

    expect($service->checkBudgetThreshold()['status'])->toBe('ok');

    ApiUsageLog::query()->update(['actual_cost' => 85.0]);
    expect($service->checkBudgetThreshold()['status'])->toBe('warning');
});

it('tracks semrush requests and errors through semrush usage service', function (): void {
    ApiServicePricing::create([
        'integration' => 'semrush',
        'endpoint' => 'backlinks_overview',
        'cost_per_unit' => 0.00005,
        'is_active' => true,
    ]);

    $budgetAlertMock = \Mockery::mock(BudgetAlertService::class);
    $budgetAlertMock->shouldReceive('checkAndAlert')->once()->andThrow(new \RuntimeException('semrush-check-failed'));
    app()->instance(BudgetAlertService::class, $budgetAlertMock);

    Log::shouldReceive('warning')
        ->once()
        ->withArgs(fn (string $message): bool => str_contains($message, 'Failed to check Semrush budget threshold'));

    $service = new SemrushUsageTrackerService;
    $log = $service->logRequest('/analytics/v1/', 'backlinks_overview', 40, ['feature' => 'backlinks']);
    $error = $service->logError('/analytics/v1/', 'backlinks_overview', 'rate-limit', ['feature' => 'backlinks']);

    expect((float) $log->estimated_cost)->toBe(0.002);
    expect($error->status)->toBe('error');
    expect($service->getTodayUnitsConsumed())->toBe(40);
    expect($service->getMonthToDateSpend())->toBe(0.002);
    expect($service->getMonthToDateUnitsConsumed())->toBe(40);
});
