<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Seeders\ExternalApis\UsageTracking\Models\ApiUsageLog;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

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

it('can create a usage log entry', function (): void {
    $log = ApiUsageLog::create([
        'integration' => 'openai',
        'model' => 'gpt-4o',
        'endpoint' => '/v1/chat/completions',
        'prompt_tokens' => 100,
        'completion_tokens' => 50,
        'total_tokens' => 150,
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
        'feature' => 'test',
        'metadata' => ['usage' => ['prompt_tokens' => 100]],
    ]);

    $log->refresh();

    expect($log->metadata)->toBeArray();
    expect($log->metadata['usage']['prompt_tokens'])->toBe(100);
});

it('scopes by integration', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'feature' => 'test']);
    ApiUsageLog::create(['integration' => 'semrush', 'feature' => 'test']);
    ApiUsageLog::create(['integration' => 'openai', 'feature' => 'test']);

    expect(ApiUsageLog::byIntegration('openai')->count())->toBe(2);
    expect(ApiUsageLog::byIntegration('semrush')->count())->toBe(1);
});

it('scopes by feature', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'feature' => 'seo_audit']);
    ApiUsageLog::create(['integration' => 'openai', 'feature' => 'content']);
    ApiUsageLog::create(['integration' => 'openai', 'feature' => 'seo_audit']);

    expect(ApiUsageLog::byFeature('seo_audit')->count())->toBe(2);
    expect(ApiUsageLog::byFeature('content')->count())->toBe(1);
});

it('scopes by model', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'model' => 'gpt-4o', 'feature' => 'test']);
    ApiUsageLog::create(['integration' => 'openai', 'model' => 'gpt-4o-mini', 'feature' => 'test']);

    expect(ApiUsageLog::byModel('gpt-4o')->count())->toBe(1);
});

it('scopes successful and failed', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'feature' => 'test', 'status' => 'success']);
    ApiUsageLog::create(['integration' => 'openai', 'feature' => 'test', 'status' => 'error']);
    ApiUsageLog::create(['integration' => 'openai', 'feature' => 'test', 'status' => 'success']);

    expect(ApiUsageLog::successful()->count())->toBe(2);
    expect(ApiUsageLog::failed()->count())->toBe(1);
});

it('scopes today', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'feature' => 'test']);

    expect(ApiUsageLog::today()->count())->toBe(1);
});

it('scopes this month', function (): void {
    ApiUsageLog::create(['integration' => 'openai', 'feature' => 'test']);

    expect(ApiUsageLog::thisMonth()->count())->toBe(1);
});

it('defines trackable morph relation', function (): void {
    $trackableModel = new class extends Model
    {
        protected $table = 'test_trackables';
    };

    $log = ApiUsageLog::create([
        'integration' => 'openai',
        'feature' => 'test',
        'trackable_type' => $trackableModel::class,
        'trackable_id' => 42,
    ]);

    $relation = $log->trackable();

    expect($relation)->toBeInstanceOf(MorphTo::class);
    expect($log->trackable_type)->toBe($trackableModel::class);
    expect($log->trackable_id)->toBe(42);
});

it('defines user relation', function (): void {
    $userModelClass = (new class extends Model {})::class;
    UsageTracking::useUserModel($userModelClass);

    $log = new ApiUsageLog;

    $relation = $log->user();

    expect($relation->getForeignKeyName())->toBe('user_id');
});
