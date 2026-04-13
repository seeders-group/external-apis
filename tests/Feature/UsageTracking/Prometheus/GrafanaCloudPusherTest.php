<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Seeders\ExternalApis\UsageTracking\Models\ApiUsageLog;
use Seeders\ExternalApis\UsageTracking\Prometheus\GrafanaCloudPusher;

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

    config()->set('external-apis.usage_tracking.grafana_cloud', [
        'enabled' => true,
        'endpoint' => 'https://prometheus-prod-01.grafana.net/api/prom/push',
        'user_id' => '123456',
        'api_token' => 'glc_test_token',
        'namespace' => '',
    ]);
});

it('pushes metrics to grafana cloud via prometheus remote write', function (): void {
    Http::fake([
        'prometheus-prod-01.grafana.net/*' => Http::response('', 200),
    ]);

    ApiUsageLog::create([
        'integration' => 'openai',
        'prompt_tokens' => 1000,
        'completion_tokens' => 500,
        'total_tokens' => 1500,
        'feature' => 'chat',
        'status' => 'success',
    ]);
    ApiUsageLog::create([
        'integration' => 'ahrefs',
        'prompt_tokens' => 0,
        'completion_tokens' => 0,
        'total_tokens' => 40,
        'feature' => 'seo',
        'status' => 'success',
    ]);

    $pusher = app(GrafanaCloudPusher::class);
    $response = $pusher->push();

    expect($response->successful())->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://prometheus-prod-01.grafana.net/api/prom/push'
            && $request->hasHeader('Authorization')
            && $request->hasHeader('Content-Type', 'application/x-protobuf')
            && $request->hasHeader('Content-Encoding', 'snappy')
            && $request->hasHeader('X-Prometheus-Remote-Write-Version', '0.1.0')
            && strlen($request->body()) > 0;
    });
});

it('sends basic auth with user id and api token', function (): void {
    Http::fake([
        'prometheus-prod-01.grafana.net/*' => Http::response('', 200),
    ]);

    ApiUsageLog::create([
        'integration' => 'openai',
        'prompt_tokens' => 100,
        'completion_tokens' => 50,
        'total_tokens' => 150,
        'feature' => 'chat',
        'status' => 'success',
    ]);

    $pusher = app(GrafanaCloudPusher::class);
    $pusher->push();

    Http::assertSent(function ($request) {
        $expectedAuth = 'Basic '.base64_encode('123456:glc_test_token');

        return $request->hasHeader('Authorization', $expectedAuth);
    });
});

it('strips trailing slash from endpoint', function (): void {
    config()->set('external-apis.usage_tracking.grafana_cloud.endpoint', 'https://prometheus-prod-01.grafana.net/api/prom/push/');

    Http::fake([
        'prometheus-prod-01.grafana.net/*' => Http::response('', 200),
    ]);

    ApiUsageLog::create([
        'integration' => 'openai',
        'prompt_tokens' => 100,
        'completion_tokens' => 50,
        'total_tokens' => 150,
        'feature' => 'chat',
        'status' => 'success',
    ]);

    $pusher = app(GrafanaCloudPusher::class);
    $pusher->push();

    Http::assertSent(fn ($request) => $request->url() === 'https://prometheus-prod-01.grafana.net/api/prom/push');
});

it('prefixes metric names with the configured namespace', function (): void {
    config()->set('external-apis.usage_tracking.grafana_cloud.namespace', 'seeders');

    Http::fake([
        'prometheus-prod-01.grafana.net/*' => Http::response('', 200),
    ]);

    ApiUsageLog::create([
        'integration' => 'openai',
        'prompt_tokens' => 100,
        'completion_tokens' => 50,
        'total_tokens' => 150,
        'feature' => 'chat',
        'status' => 'success',
    ]);

    $pusher = app(GrafanaCloudPusher::class);
    $response = $pusher->push();

    expect($response->successful())->toBeTrue();

    Http::assertSent(function ($request) {
        $body = $request->body();

        // The binary protobuf body should contain the namespaced metric name
        return str_contains($body, 'seeders_external_apis_requests_total')
            && str_contains($body, 'seeders_external_apis_total_tokens_total')
            && ! str_contains($body, '__name__external_apis_requests_total');
    });
});
