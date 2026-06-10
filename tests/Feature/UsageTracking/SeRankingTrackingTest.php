<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\SeRanking\Requests\GetSites;
use Seeders\ExternalApis\Integrations\SeRanking\SeRankingConnector;
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

it('requires tracking context for se ranking requests', function (): void {
    $connector = new SeRankingConnector;
    $connector->withMockClient(new MockClient([
        GetSites::class => MockResponse::make([], 200),
    ]));

    expect(fn (): Response => $connector->send(new GetSites))
        ->toThrow(RuntimeException::class, 'requires tracking context');
});

it('records an api_log for a get sites request', function (): void {
    $connector = SeRankingConnector::forScope('se_ranking_tracking_test');
    $connector->withMockClient(new MockClient([
        GetSites::class => MockResponse::make([], 200),
    ]));

    $connector->send(new GetSites);

    $apiLog = ApiConsumptionLog::query()->first();

    expect($apiLog)->not->toBeNull();
    expect($apiLog->integration)->toBe('se-ranking');
    expect($apiLog->scope)->toBe('se_ranking_tracking_test');
    expect($apiLog->endpoint)->toBe('/sites');
});

it('records trackable model metadata in api_logs when using forModel', function (): void {
    $model = new SeRankingTrackableModel;
    $model->setAttribute($model->getKeyName(), 42);
    $model->exists = true;

    $connector = SeRankingConnector::forModel($model, 'se_ranking_tracking_test');
    $connector->withMockClient(new MockClient([
        GetSites::class => MockResponse::make([], 200),
    ]));

    $connector->send(new GetSites);

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect($apiLog->trackable_type)->toBe($model->getMorphClass());
    expect((int) $apiLog->trackable_id)->toBe(42);
    expect($apiLog->scope)->toBe('se_ranking_tracking_test');
});

it('logs failed se ranking requests', function (): void {
    $connector = SeRankingConnector::forScope('se_ranking_tracking_test');
    $connector->withMockClient(new MockClient([
        GetSites::class => MockResponse::make('internal error', 500),
    ]));

    $connector->send(new GetSites);

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect($apiLog->status)->toBe(500);
});

class SeRankingTrackableModel extends Model
{
    protected $table = 'projects';
}
