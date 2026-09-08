<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use Seeders\ExternalApis\Integrations\GoogleSearch\GoogleSearchConnector;
use Seeders\ExternalApis\Integrations\GoogleSearch\Requests\CustomSearchRequest;
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

it('requires tracking context for google search requests', function (): void {
    $connector = new GoogleSearchConnector;
    $connector->withMockClient(new MockClient([
        CustomSearchRequest::class => MockResponse::make([], 200),
    ]));

    expect(fn (): Response => $connector->send(new CustomSearchRequest('laravel framework')))
        ->toThrow(RuntimeException::class, 'requires tracking context');
});

it('records an api_log for a custom search request', function (): void {
    $connector = GoogleSearchConnector::forScope('google_search_tracking_test');
    $connector->withMockClient(new MockClient([
        CustomSearchRequest::class => MockResponse::make([], 200),
    ]));

    $connector->send(new CustomSearchRequest('laravel framework'));

    $apiLog = ApiConsumptionLog::query()->first();

    expect($apiLog)->not->toBeNull();
    expect($apiLog->integration)->toBe('google_search');
    expect($apiLog->scope)->toBe('google_search_tracking_test');
    expect($apiLog->endpoint)->toBe('/customsearch/v1');
});

it('counts each custom search request as a single billable query', function (): void {
    $connector = GoogleSearchConnector::forScope('google_search_tracking_test');
    $connector->withMockClient(new MockClient([
        CustomSearchRequest::class => MockResponse::make([], 200),
    ]));

    $connector->send(new CustomSearchRequest('laravel framework'));

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect((float) $apiLog->consumption)->toBe(1.0);
    expect($apiLog->consumption_type)->toBe('requests');
});

it('records trackable model metadata in api_logs when using forModel', function (): void {
    $model = new GoogleSearchTrackableModel;
    $model->setAttribute($model->getKeyName(), 42);
    $model->exists = true;

    $connector = GoogleSearchConnector::forModel($model, 'google_search_tracking_test');
    $connector->withMockClient(new MockClient([
        CustomSearchRequest::class => MockResponse::make([], 200),
    ]));

    $connector->send(new CustomSearchRequest('laravel framework'));

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect($apiLog->trackable_type)->toBe($model->getMorphClass());
    expect((int) $apiLog->trackable_id)->toBe(42);
    expect($apiLog->scope)->toBe('google_search_tracking_test');
});

it('logs failed google search requests', function (): void {
    $connector = GoogleSearchConnector::forScope('google_search_tracking_test');
    $connector->withMockClient(new MockClient([
        CustomSearchRequest::class => MockResponse::make('quota exceeded', 429),
    ]));

    $connector->send(new CustomSearchRequest('laravel framework'));

    $apiLog = ApiConsumptionLog::query()->latest()->first();

    expect($apiLog->status)->toBe(429);
});

class GoogleSearchTrackableModel extends Model
{
    protected $table = 'projects';
}
