<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use Seeders\ExternalApis\Facades\Hunter;
use Seeders\ExternalApis\Integrations\Hunter\HunterConnector;
use Seeders\ExternalApis\Integrations\Hunter\Requests\DomainSearchRequest;
use Seeders\ExternalApis\UsageTracking\Models\ApiConsumptionLog;

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

afterEach(function (): void {
    Mockery::close();
});

it('requires tracking context for hunter requests', function (): void {
    $connector = new HunterConnector;
    $connector->withMockClient(new MockClient([
        DomainSearchRequest::class => MockResponse::make([], 200),
    ]));

    expect(fn (): Response => $connector->send(new DomainSearchRequest('example.com')))
        ->toThrow(RuntimeException::class, 'requires tracking context');
});

it('records an api_log for a domain search request', function (): void {
    $connector = HunterConnector::forScope('hunter_tracking_test');
    $connector->withMockClient(new MockClient([
        DomainSearchRequest::class => MockResponse::make([], 200),
    ]));

    $connector->send(new DomainSearchRequest('example.com'));

    $apiLog = ApiConsumptionLog::query()->first();

    expect($apiLog)->not->toBeNull();
    expect($apiLog->integration)->toBe('hunter');
    expect($apiLog->scope)->toBe('hunter_tracking_test');
    expect($apiLog->endpoint)->toBe('/domain-search');
});

it('counts each domain search request as a single billable search', function (): void {
    $connector = HunterConnector::forScope('hunter_tracking_test');
    $connector->withMockClient(new MockClient([
        DomainSearchRequest::class => MockResponse::make([], 200),
    ]));

    $connector->send(new DomainSearchRequest('example.com'));

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect((float) $apiLog->consumption)->toBe(1.0);
    expect($apiLog->consumption_type)->toBe('requests');
});

it('records trackable model metadata in api_logs when using forModel', function (): void {
    $model = new HunterTrackableModel;
    $model->setAttribute($model->getKeyName(), 42);
    $model->exists = true;

    $connector = HunterConnector::forModel($model, 'hunter_tracking_test');
    $connector->withMockClient(new MockClient([
        DomainSearchRequest::class => MockResponse::make([], 200),
    ]));

    $connector->send(new DomainSearchRequest('example.com'));

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect($apiLog->trackable_type)->toBe($model->getMorphClass());
    expect((int) $apiLog->trackable_id)->toBe(42);
    expect($apiLog->scope)->toBe('hunter_tracking_test');
});

it('logs hunter requests that exhaust the monthly quota', function (): void {
    $connector = HunterConnector::forScope('hunter_tracking_test');
    $connector->withMockClient(new MockClient([
        DomainSearchRequest::class => MockResponse::make([
            'errors' => [
                ['id' => 'no_quota_left', 'code' => 429, 'details' => 'You have reached your monthly requests limit.'],
            ],
        ], 429),
    ]));

    $connector->send(new DomainSearchRequest('example.com'));

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect($apiLog->status)->toBe(429);

    // A rejected call still counts as one recorded request: api_logs measures
    // calls made, not what Hunter actually billed. Pinned so that changing the
    // middleware's failure semantics is a deliberate decision.
    expect((float) $apiLog->consumption)->toBe(1.0);
});

it('tracks hunter requests sent through the facade', function (): void {
    Hunter::withScope('hunter_facade_test');

    Hunter::getFacadeRoot()->withMockClient(new MockClient([
        DomainSearchRequest::class => MockResponse::make([], 200),
    ]));

    Hunter::send(new DomainSearchRequest('example.com'));

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect($apiLog)->not->toBeNull();
    expect($apiLog->integration)->toBe('hunter');
    expect($apiLog->scope)->toBe('hunter_facade_test');
});

class HunterTrackableModel extends Model
{
    protected $table = 'projects';
}
