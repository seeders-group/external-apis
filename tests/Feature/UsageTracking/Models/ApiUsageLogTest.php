<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Seeders\ExternalApis\UsageTracking\Models\AiModelPricing;
use Seeders\ExternalApis\UsageTracking\Models\ApiUsageLog;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

beforeEach(function (): void {
    Schema::dropIfExists('api_usage_logs');
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

it('can create a usage log entry', function (): void {
    $log = ApiUsageLog::create([
        'integration' => 'openai',
        'model' => 'gpt-4o',
        'endpoint' => '/v1/chat/completions',
        'prompt_tokens' => 100,
        'completion_tokens' => 50,
        'total_tokens' => 150,
        'estimated_cost' => 0.001,
        'feature' => 'content_generation',
        'status' => 'success',
    ]);

    expect($log->id)->not->toBeNull();
    expect($log->integration)->toBe('openai');
    expect($log->prompt_tokens)->toBe(100);
    expect($log->completion_tokens)->toBe(50);
});

it('casts metadata to array', function (): void {
    $log = ApiUsageLog::create([
        'integration' => 'openai',
        'estimated_cost' => 0.001,
        'feature' => 'test',
        'metadata' => ['usage' => ['prompt_tokens' => 100]],
    ]);

    $log->refresh();

    expect($log->metadata)->toBeArray();
    expect($log->metadata['usage']['prompt_tokens'])->toBe(100);
});

it('scopes by integration', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.01, 'feature' => 'test']);
    ApiUsageLog::create(['integration' => 'semrush', 'estimated_cost' => 0.02, 'feature' => 'test']);
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.03, 'feature' => 'test']);

    expect(ApiUsageLog::byIntegration('openai')->count())->toBe(2);
    expect(ApiUsageLog::byIntegration('semrush')->count())->toBe(1);
});

it('scopes by feature', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.01, 'feature' => 'seo_audit']);
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.02, 'feature' => 'content']);
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.03, 'feature' => 'seo_audit']);

    expect(ApiUsageLog::byFeature('seo_audit')->count())->toBe(2);
    expect(ApiUsageLog::byFeature('content')->count())->toBe(1);
});

it('scopes by model', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'model' => 'gpt-4o', 'estimated_cost' => 0.01, 'feature' => 'test']);
    ApiUsageLog::create(['integration' => 'openai', 'model' => 'gpt-4o-mini', 'estimated_cost' => 0.01, 'feature' => 'test']);

    expect(ApiUsageLog::byModel('gpt-4o')->count())->toBe(1);
});

it('scopes successful and failed', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.01, 'feature' => 'test', 'status' => 'success']);
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.02, 'feature' => 'test', 'status' => 'error']);
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.03, 'feature' => 'test', 'status' => 'success']);

    expect(ApiUsageLog::successful()->count())->toBe(2);
    expect(ApiUsageLog::failed()->count())->toBe(1);
});

it('scopes reconciled and unreconciled', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.01, 'feature' => 'test', 'reconciled_at' => now()]);
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.02, 'feature' => 'test', 'reconciled_at' => null]);

    expect(ApiUsageLog::reconciled()->count())->toBe(1);
    expect(ApiUsageLog::unreconciled()->count())->toBe(1);
});

it('scopes today', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.01, 'feature' => 'test']);

    expect(ApiUsageLog::today()->count())->toBe(1);
});

it('scopes this month', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'estimated_cost' => 0.01, 'feature' => 'test']);

    expect(ApiUsageLog::thisMonth()->count())->toBe(1);
});

it('has cost in dollars accessor', function (): void {
    $log = ApiUsageLog::create([
        'integration' => 'openai',
        'estimated_cost' => 1.2345,
        'feature' => 'test',
    ]);

    expect($log->cost_in_dollars)->toBe('$1.2345');
});

it('has is reconciled accessor', function (): void {
    $reconciledLog = ApiUsageLog::create([
        'integration' => 'openai',
        'estimated_cost' => 0.01,
        'feature' => 'test',
        'reconciled_at' => now(),
    ]);

    $unreconciledLog = ApiUsageLog::create([
        'integration' => 'openai',
        'estimated_cost' => 0.01,
        'feature' => 'test',
        'reconciled_at' => null,
    ]);

    expect($reconciledLog->is_reconciled)->toBeTrue();
    expect($unreconciledLog->is_reconciled)->toBeFalse();
});

it('calculates cost from tokens using database pricing', function (): void {
    AiModelPricing::create([
        'integration' => 'openai',
        'model' => 'gpt-4o',
        'input_per_1m_tokens' => 2.50,
        'output_per_1m_tokens' => 10.00,
    ]);

    // 1000 input tokens = 1000/1M * 2.50 = 0.0025
    // 500 output tokens = 500/1M * 10.00 = 0.005
    $cost = ApiUsageLog::calculateCostFromTokens('gpt-4o', 1000, 500);

    expect($cost)->toBe(0.0075);
});

it('calculates cost with cached tokens', function (): void {
    AiModelPricing::create([
        'integration' => 'openai',
        'model' => 'gpt-4o',
        'input_per_1m_tokens' => 2.50,
        'output_per_1m_tokens' => 10.00,
        'cached_input_per_1m_tokens' => 1.25,
    ]);

    // 700 regular input tokens = 700/1M * 2.50 = 0.00175
    // 300 cached input tokens = 300/1M * 1.25 = 0.000375
    // 500 output tokens = 500/1M * 10.00 = 0.005
    $cost = ApiUsageLog::calculateCostFromTokens('gpt-4o', 1000, 500, 300);

    expect($cost)->toBe(0.007125);
});

it('returns zero cost for unknown model', function (): void {
    $cost = ApiUsageLog::calculateCostFromTokens('unknown-model', 1000, 500);

    expect($cost)->toBe(0.0);
});

it('calculates total cost from grouped logs', function (): void {
    AiModelPricing::create([
        'integration' => 'openai',
        'model' => 'gpt-4o',
        'input_per_1m_tokens' => 2.5,
        'output_per_1m_tokens' => 10.0,
        'cached_input_per_1m_tokens' => 1.25,
    ]);

    $logs = collect([
        ApiUsageLog::make([
            'model' => 'gpt-4o',
            'prompt_tokens' => 500,
            'completion_tokens' => 250,
            'input_cached_tokens' => 100,
        ]),
        ApiUsageLog::make([
            'model' => 'gpt-4o',
            'prompt_tokens' => 500,
            'completion_tokens' => 250,
            'input_cached_tokens' => 200,
        ]),
    ]);

    $cost = ApiUsageLog::calculateTotalCostFromLogs($logs);

    expect($cost)->toBe(0.007125);
});

it('defines user relation', function (): void {
    $userModelClass = get_class(new class extends \Illuminate\Database\Eloquent\Model {});
    UsageTracking::useUserModel($userModelClass);

    $log = new ApiUsageLog;

    $relation = $log->user();

    expect($relation->getForeignKeyName())->toBe('user_id');
});
