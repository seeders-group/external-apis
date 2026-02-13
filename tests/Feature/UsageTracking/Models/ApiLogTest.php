<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Seeders\ExternalApis\UsageTracking\Models\ApiLog;

beforeEach(function (): void {
    Schema::dropIfExists('api_logs');

    Schema::create('api_logs', function (Blueprint $table): void {
        $table->id();
        $table->nullableMorphs('trackable');
        $table->string('scope')->nullable();
        $table->string('integration');
        $table->string('endpoint');
        $table->integer('status')->default(200);
        $table->decimal('consumption', 12, 6)->default(0);
        $table->string('consumption_type')->nullable();
        $table->integer('latency_ms')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamps();
    });
});

it('can create an api log entry', function (): void {
    $log = ApiLog::create([
        'integration' => 'ahrefs',
        'endpoint' => '/v3/site-explorer/domain-rating',
        'status' => 200,
        'consumption' => 1.0,
        'consumption_type' => 'units',
    ]);

    expect($log->id)->not->toBeNull();
    expect($log->integration)->toBe('ahrefs');
    expect($log->status)->toBe(200);
});

it('stores trackable morph data', function (): void {
    $log = ApiLog::create([
        'trackable_type' => 'App\\Models\\Project',
        'trackable_id' => 42,
        'scope' => 'seo_audit',
        'integration' => 'semrush',
        'endpoint' => '/analytics/v1/',
        'status' => 200,
        'consumption' => 40.0,
        'consumption_type' => 'units',
    ]);

    expect($log->trackable_type)->toBe('App\\Models\\Project');
    expect($log->trackable_id)->toBe(42);
    expect($log->scope)->toBe('seo_audit');
});

it('casts metadata to json', function (): void {
    $log = ApiLog::create([
        'integration' => 'ahrefs',
        'endpoint' => '/test',
        'metadata' => ['units' => ['actual' => 5]],
    ]);

    $log->refresh();

    expect($log->metadata)->toBeArray();
    expect($log->metadata['units']['actual'])->toBe(5);
});

it('scopes by integration', function (): void {
    ApiLog::create(['integration' => 'ahrefs', 'endpoint' => '/test', 'status' => 200]);
    ApiLog::create(['integration' => 'semrush', 'endpoint' => '/test', 'status' => 200]);
    ApiLog::create(['integration' => 'ahrefs', 'endpoint' => '/test', 'status' => 200]);

    expect(ApiLog::forIntegration('ahrefs')->count())->toBe(2);
    expect(ApiLog::forIntegration('semrush')->count())->toBe(1);
});

it('scopes by scope name', function (): void {
    ApiLog::create(['integration' => 'ahrefs', 'endpoint' => '/test', 'scope' => 'seo_audit']);
    ApiLog::create(['integration' => 'ahrefs', 'endpoint' => '/test', 'scope' => 'link_building']);

    expect(ApiLog::forScope('seo_audit')->count())->toBe(1);
});

it('scopes successful responses', function (): void {
    ApiLog::create(['integration' => 'ahrefs', 'endpoint' => '/test', 'status' => 200]);
    ApiLog::create(['integration' => 'ahrefs', 'endpoint' => '/test', 'status' => 201]);
    ApiLog::create(['integration' => 'ahrefs', 'endpoint' => '/test', 'status' => 500]);
    ApiLog::create(['integration' => 'ahrefs', 'endpoint' => '/test', 'status' => 401]);

    expect(ApiLog::successful()->count())->toBe(2);
    expect(ApiLog::failed()->count())->toBe(2);
});

it('scopes today', function (): void {
    ApiLog::create(['integration' => 'ahrefs', 'endpoint' => '/test']);

    expect(ApiLog::today()->count())->toBe(1);
});

it('scopes this month', function (): void {
    ApiLog::create(['integration' => 'ahrefs', 'endpoint' => '/test']);

    expect(ApiLog::thisMonth()->count())->toBe(1);
});
