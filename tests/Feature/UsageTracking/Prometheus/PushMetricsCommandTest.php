<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Seeders\ExternalApis\UsageTracking\Models\AiUsageLog;

beforeEach(function (): void {
    Schema::dropIfExists('api_usage_logs');
    Schema::dropIfExists('api_logs');

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

    Schema::create('api_logs', function (Blueprint $table): void {
        $table->id();
        $table->string('integration', 50)->index();
        $table->string('scope')->nullable();
        $table->nullableMorphs('trackable');
        $table->string('endpoint', 100)->nullable();
        $table->integer('status')->nullable();
        $table->decimal('consumption', 12, 6)->default(0);
        $table->json('metadata')->nullable();
        $table->timestamps();
    });
});

it('warns and exits successfully when grafana cloud is disabled', function (): void {
    config()->set('external-apis.usage_tracking.grafana_cloud.enabled', false);

    $this->artisan('external-apis:push-metrics')
        ->expectsOutput('Grafana Cloud metrics push is disabled.')
        ->assertSuccessful();
});

it('pushes metrics and reports success', function (): void {
    config()->set('external-apis.usage_tracking.grafana_cloud', [
        'enabled' => true,
        'endpoint' => 'https://prometheus-prod-01.grafana.net/api/prom/push',
        'user_id' => '123456',
        'api_token' => 'glc_test_token',
    ]);

    Http::fake([
        'prometheus-prod-01.grafana.net/*' => Http::response('', 200),
    ]);

    AiUsageLog::create([
        'integration' => 'openai',
        'prompt_tokens' => 100,
        'completion_tokens' => 50,
        'total_tokens' => 150,
        'feature' => 'chat',
        'status' => 'success',
    ]);

    $this->artisan('external-apis:push-metrics')
        ->expectsOutput('Metrics pushed to Grafana Cloud successfully.')
        ->assertSuccessful();
});

it('reports failure when grafana cloud returns an error', function (): void {
    config()->set('external-apis.usage_tracking.grafana_cloud', [
        'enabled' => true,
        'endpoint' => 'https://prometheus-prod-01.grafana.net/api/prom/push',
        'user_id' => '123456',
        'api_token' => 'wrong_token',
    ]);

    Http::fake([
        'prometheus-prod-01.grafana.net/*' => Http::response('Unauthorized', 401),
    ]);

    AiUsageLog::create([
        'integration' => 'openai',
        'prompt_tokens' => 100,
        'completion_tokens' => 50,
        'total_tokens' => 150,
        'feature' => 'chat',
        'status' => 'success',
    ]);

    $this->artisan('external-apis:push-metrics')
        ->assertFailed();
});
