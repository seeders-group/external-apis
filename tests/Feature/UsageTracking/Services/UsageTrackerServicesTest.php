<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Prism\Prism\Enums\Provider;
use Seeders\ExternalApis\UsageTracking\Services\AhrefsUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\DataForSeoUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\OpenAIUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\PrismUsageTrackerService;

beforeEach(function (): void {
    Schema::dropIfExists('api_usage_logs');

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
        $table->string('feature', 100)->index();
        $table->string('sub_feature', 100)->nullable();
        $table->unsignedBigInteger('project_id')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->nullableMorphs('trackable');
        $table->string('status', 20)->default('success');
        $table->text('error_message')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamps();
    });
});

afterEach(function (): void {
    Mockery::close();
});

it('tracks openai requests, errors, and image generation', function (): void {
    $service = new OpenAIUsageTrackerService;
    $usage = $service->logRequest('gpt-test', 1_000_000, 500_000, ['feature' => 'chat'], 250_000, 'req-1');
    $image = $service->logImageGeneration('dall-e-test', 2, '1024x1024', 'hd', ['feature' => 'images']);
    $error = $service->logError('gpt-test', 'network timeout', ['feature' => 'chat']);

    expect($usage->integration)->toBe('openai');
    expect($usage->prompt_tokens)->toBe(1_000_000);
    expect($usage->completion_tokens)->toBe(500_000);
    expect($usage->input_cached_tokens)->toBe(250_000);
    expect($image->images_generated)->toBe(2);
    expect($error->status)->toBe('error');
});

it('tracks ahrefs usage', function (): void {
    $service = new AhrefsUsageTrackerService;
    $log = $service->logRequest('/site-explorer/backlinks', 40, ['feature' => 'seo']);
    $error = $service->logError('/site-explorer/backlinks', 'rate limit');

    expect($log->total_tokens)->toBe(40);
    expect($error->status)->toBe('error');
    expect($service->getTodayUnitsConsumed())->toBe(40);
    expect($service->getMonthToDateUnitsConsumed())->toBe(40);
});

it('tracks dataforseo usage with endpoint-derived features', function (): void {
    $service = new DataForSeoUsageTrackerService;
    $service->logRequest('/v3/business_data/google/reviews/task_get/advanced');
    $service->logRequest('/v3/serp/google/organic/live/advanced');
    $error = $service->logError('/v3/merchant/google/products/task_get', 'api error');

    expect($error->feature)->toBe('merchant');
    expect($service->getTodayRequests())->toBe(3);
    expect($service->getMonthToDateRequests())->toBe(3);
});

it('tracks prism usage across text, embeddings, image, and audio paths', function (): void {
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

    expect($text->integration)->toBe('openai');
    expect($text->metadata['thought_tokens'])->toBe(100);
    expect($embedding->total_tokens)->toBe(500_000);
    expect($image->images_generated)->toBe(2);
    expect($audio->characters_processed)->toBe(300_000);
    expect($error->status)->toBe('error');
    expect($service->providerToIntegration(Provider::OpenAI))->toBe('openai');
})->skip(! class_exists(Provider::class), 'Prism is not installed');

it('stores trackable_type and trackable_id from prism context', function (): void {
    $service = new PrismUsageTrackerService;

    $log = $service->logRequest(
        Provider::OpenAI,
        'gpt-trackable',
        1000,
        500,
        [
            'feature' => 'test',
            'trackable_type' => 'App\\Models\\Project',
            'trackable_id' => 99,
        ],
    );

    expect($log->trackable_type)->toBe('App\\Models\\Project');
    expect($log->trackable_id)->toBe(99);

    $error = $service->logError(
        Provider::OpenAI,
        'gpt-trackable',
        'test error',
        [
            'feature' => 'test',
            'trackable_type' => 'App\\Models\\Project',
            'trackable_id' => 99,
        ],
    );

    expect($error->trackable_type)->toBe('App\\Models\\Project');
    expect($error->trackable_id)->toBe(99);
})->skip(! class_exists(Provider::class), 'Prism is not installed');
