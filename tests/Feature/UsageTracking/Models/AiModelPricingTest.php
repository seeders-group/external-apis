<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Seeders\ExternalApis\UsageTracking\Models\AiModelPricing;

beforeEach(function (): void {
    Schema::dropIfExists('ai_model_pricing');

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

it('returns db pricing before config fallback', function (): void {
    config()->set('external-apis.usage_tracking.pricing.openai.models.gpt-4o', [
        'input_per_1m_tokens' => 99.0,
        'output_per_1m_tokens' => 99.0,
    ]);

    AiModelPricing::create([
        'integration' => 'openai',
        'model' => 'gpt-4o',
        'input_per_1m_tokens' => 2.5,
        'output_per_1m_tokens' => 10.0,
        'cached_input_per_1m_tokens' => 1.25,
    ]);

    $pricing = AiModelPricing::getPricing('gpt-4o', 'openai');

    expect($pricing)->toBe([
        'input_per_1m_tokens' => 2.5,
        'output_per_1m_tokens' => 10.0,
        'cached_input_per_1m_tokens' => 1.25,
    ]);
});

it('merges config and db pricing with db precedence in getAllPricing', function (): void {
    config()->set('external-apis.usage_tracking.pricing.openai.models.gpt-config', [
        'input_per_1m_tokens' => 3.0,
        'output_per_1m_tokens' => 6.0,
    ]);

    AiModelPricing::create([
        'integration' => 'openai',
        'model' => 'gpt-db',
        'input_per_1m_tokens' => 1.0,
        'output_per_1m_tokens' => 2.0,
        'cached_input_per_1m_tokens' => null,
    ]);

    AiModelPricing::create([
        'integration' => 'openai',
        'model' => 'gpt-config',
        'input_per_1m_tokens' => 4.0,
        'output_per_1m_tokens' => 8.0,
        'cached_input_per_1m_tokens' => 2.0,
    ]);

    $allPricing = AiModelPricing::getAllPricing('openai');

    expect($allPricing)->toHaveKey('gpt-db');
    expect($allPricing)->toHaveKey('gpt-config');
    expect($allPricing['gpt-db']['output_per_1m_tokens'])->toBe(2.0);
    expect($allPricing['gpt-config'])->toBe([
        'input_per_1m_tokens' => 4.0,
        'output_per_1m_tokens' => 8.0,
        'cached_input_per_1m_tokens' => 2.0,
    ]);
});
