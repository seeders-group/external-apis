<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\Majestic\MajesticConnector;
use Seeders\ExternalApis\Integrations\Majestic\Requests\GetIndexItemInfo;
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

it('requires tracking context for majestic requests', function (): void {
    $connector = new MajesticConnector;
    $connector->withMockClient(new MockClient([
        GetIndexItemInfo::class => MockResponse::make([], 200),
    ]));

    expect(fn (): Response => $connector->send(new GetIndexItemInfo('example.com')))
        ->toThrow(RuntimeException::class, 'requires tracking context');
});

it('records an api_log for an index item info request', function (): void {
    $connector = MajesticConnector::forScope('majestic_tracking_test');
    $connector->withMockClient(new MockClient([
        GetIndexItemInfo::class => MockResponse::make([], 200),
    ]));

    $connector->send(new GetIndexItemInfo('example.com'));

    $apiLog = ApiConsumptionLog::query()->first();

    expect($apiLog)->not->toBeNull();
    expect($apiLog->integration)->toBe('majestic');
    expect($apiLog->scope)->toBe('majestic_tracking_test');
    expect($apiLog->endpoint)->toBe('/');
});

it('records trackable model metadata in api_logs when using forModel', function (): void {
    $model = new MajesticTrackableModel;
    $model->setAttribute($model->getKeyName(), 42);
    $model->exists = true;

    $connector = MajesticConnector::forModel($model, 'majestic_tracking_test');
    $connector->withMockClient(new MockClient([
        GetIndexItemInfo::class => MockResponse::make([], 200),
    ]));

    $connector->send(new GetIndexItemInfo('example.com'));

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect($apiLog->trackable_type)->toBe($model->getMorphClass());
    expect((int) $apiLog->trackable_id)->toBe(42);
    expect($apiLog->scope)->toBe('majestic_tracking_test');
});

it('logs failed majestic requests', function (): void {
    $connector = MajesticConnector::forScope('majestic_tracking_test');
    $connector->withMockClient(new MockClient([
        GetIndexItemInfo::class => MockResponse::make('internal error', 500),
    ]));

    $connector->send(new GetIndexItemInfo('example.com'));

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect($apiLog->status)->toBe(500);
});

class MajesticTrackableModel extends Model
{
    protected $table = 'projects';
}
