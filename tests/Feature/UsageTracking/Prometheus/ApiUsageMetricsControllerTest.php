<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Seeders\ExternalApis\UsageTracking\Models\ApiUsageLog;

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

    config()->set('external-apis.usage_tracking.prometheus.enabled', true);
    config()->set('external-apis.usage_tracking.prometheus.route', 'metrics/external-apis');
    config()->set('external-apis.usage_tracking.prometheus.middleware', []);
    config()->set('external-apis.usage_tracking.prometheus.token', null);
});

it('exposes aggregated metrics in prometheus format', function (): void {
    ApiUsageLog::create([
        'integration' => 'openai',
        'prompt_tokens' => 1000,
        'completion_tokens' => 500,
        'total_tokens' => 1500,
        'feature' => 'chat',
        'status' => 'success',
    ]);
    ApiUsageLog::create([
        'integration' => 'openai',
        'prompt_tokens' => 200,
        'completion_tokens' => 100,
        'total_tokens' => 300,
        'feature' => 'chat',
        'status' => 'error',
    ]);
    ApiUsageLog::create([
        'integration' => 'ahrefs',
        'prompt_tokens' => 0,
        'completion_tokens' => 0,
        'total_tokens' => 40,
        'feature' => 'seo',
        'status' => 'success',
    ]);

    $response = $this->get('/metrics/external-apis');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');

    $body = $response->getContent();

    expect($body)->toContain('# TYPE external_apis_requests_total counter');
    expect($body)->toContain('external_apis_requests_total{integration="openai",status="success"} 1');
    expect($body)->toContain('external_apis_requests_total{integration="openai",status="error"} 1');
    expect($body)->toContain('external_apis_requests_total{integration="ahrefs",status="success"} 1');
    expect($body)->toContain('external_apis_total_tokens_total{integration="ahrefs",status="success"} 40');
});

it('rejects requests without the configured token', function (): void {
    config()->set('external-apis.usage_tracking.prometheus.token', 'secret-token');

    $this->get('/metrics/external-apis')->assertStatus(401);
    $this->get('/metrics/external-apis?token=wrong')->assertStatus(401);
});

it('accepts the configured token via bearer header, custom header and query string', function (): void {
    config()->set('external-apis.usage_tracking.prometheus.token', 'secret-token');

    $this->withHeaders(['Authorization' => 'Bearer secret-token'])
        ->get('/metrics/external-apis')
        ->assertOk();

    $this->withHeaders(['X-Prometheus-Token' => 'secret-token'])
        ->get('/metrics/external-apis')
        ->assertOk();

    $this->get('/metrics/external-apis?token=secret-token')->assertOk();
});
