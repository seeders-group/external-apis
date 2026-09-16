<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
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
    // The documented form: withScope() returns the connector the call chains on.
    $connector = Hunter::withScope('hunter_facade_test');

    $connector->withMockClient(new MockClient([
        DomainSearchRequest::class => MockResponse::make([], 200),
    ]));

    $connector->send(new DomainSearchRequest('example.com'));

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect($apiLog)->not->toBeNull();
    expect($apiLog->integration)->toBe('hunter');
    expect($apiLog->scope)->toBe('hunter_facade_test');
});

it('boots tracking for every request a reused connector sends', function (): void {
    // PHP reuses an object id once the previous pending request is freed, which
    // a real send does as soon as the caller drops the Response. Keying the
    // double-boot guard on the id therefore matched a stale entry and skipped
    // tracking on every send after the first.
    $connector = HunterConnector::forScope('hunter_reuse_test');

    $bootRan = [];

    for ($i = 0; $i < 5; $i++) {
        // Constructing a PendingRequest is what boots Saloon's plugins.
        $pendingRequest = new PendingRequest($connector, new DomainSearchRequest("site{$i}.com"));

        // The scope header is only added when boot does not return early.
        $bootRan[] = $pendingRequest->headers()->get('X-Seeders-Scope') === 'hunter_reuse_test';

        unset($pendingRequest);
    }

    expect($bootRan)->toBe([true, true, true, true, true]);
});

it('still boots a single pending request only once', function (): void {
    $connector = HunterConnector::forScope('hunter_double_boot_test');

    // Construction already boots the plugin and attaches the recording middleware.
    $pendingRequest = new PendingRequest($connector, new DomainSearchRequest('example.com'));

    expect($pendingRequest->middleware()->getResponsePipeline()->getPipes())->toHaveCount(1);

    // Booting the same pending request again must not attach it a second time.
    $connector->bootTracksApiUsage($pendingRequest);

    expect($pendingRequest->middleware()->getResponsePipeline()->getPipes())->toHaveCount(1);
});

it('does not leak tracking context between facade calls', function (): void {
    Hunter::withScope('first_feature');

    // A cached facade root would hand the next caller the scope above.
    expect(fn (): Response => Hunter::send(new DomainSearchRequest('example.com')))
        ->toThrow(RuntimeException::class, 'requires tracking context');
});

class HunterTrackableModel extends Model
{
    protected $table = 'projects';
}
